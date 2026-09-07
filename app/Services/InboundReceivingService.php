<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\InventoryStatus;
use App\Enums\LocationType;
use App\Enums\MovementDirection;
use App\Enums\PutawayStatus;
use App\Enums\StockMovementType;
use App\Models\GoodsReceipt;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Pallet;
use App\Models\ProductionReceipt;
use App\Models\PutawayTask;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InboundReceivingService
{
    /**
     * Dapatkan atau cari default staging/receiving location untuk warehouse.
     */
    public function getOrCreateStagingLocation(int $warehouseId): Location
    {
        $staging = Location::query()
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->whereIn('location_type', [LocationType::Staging, LocationType::Receiving])
            ->orderByRaw("CASE WHEN location_type = 'staging' THEN 1 ELSE 2 END")
            ->first();

        if (! $staging) {
            $staging = Location::create([
                'warehouse_id' => $warehouseId,
                'code' => 'STAGING-' . $warehouseId,
                'name' => 'Inbound Staging Area',
                'location_type' => LocationType::Staging,
                'is_pickable' => false,
                'is_putaway_allowed' => false,
                'is_active' => true,
            ]);
        }

        return $staging;
    }

    /**
     * Dapatkan default storage destination bin untuk putaway.
     */
    public function getDefaultStorageBin(int $warehouseId): Location
    {
        $bin = Location::query()
            ->where('warehouse_id', $warehouseId)
            ->where('is_active', true)
            ->where('is_putaway_allowed', true)
            ->first();

        if (! $bin) {
            $bin = Location::create([
                'warehouse_id' => $warehouseId,
                'code' => 'DEFAULT-BIN-' . $warehouseId,
                'name' => 'Default Storage Bin',
                'location_type' => LocationType::Bin,
                'is_pickable' => true,
                'is_putaway_allowed' => true,
                'is_active' => true,
            ]);
        }

        return $bin;
    }

    /**
     * Konfirmasi penerimaan Goods Receipt (GRN).
     * Atomic & idempotent: tidak membuat duplicate task/stock jika dipanggil ulang.
     */
    public function confirmGoodsReceipt(GoodsReceipt $goodsReceipt, ?int $userId = null): GoodsReceipt
    {
        return DB::transaction(function () use ($goodsReceipt, $userId) {
            $goodsReceipt->refresh();

            // Idempotency check: jika sudah approved / completed dan task sudah ada
            if ($goodsReceipt->putawayTasks()->exists() && $goodsReceipt->status === DocumentStatus::APPROVED) {
                return $goodsReceipt;
            }

            $userId = $userId ?? Auth::id() ?? $goodsReceipt->received_by;
            $warehouseId = $goodsReceipt->warehouse_id;
            $stagingLocation = $this->getOrCreateStagingLocation($warehouseId);
            $defaultStorageBin = $this->getDefaultStorageBin($warehouseId);

            foreach ($goodsReceipt->items as $item) {
                $acceptedQty = (float) $item->accepted_qty;
                if ($acceptedQty <= 0) {
                    continue;
                }

                // Periksa apakah item ini sudah memiliki putaway task
                $existingTask = PutawayTask::where('goods_receipt_item_id', $item->id)->first();
                if ($existingTask) {
                    continue;
                }

                // Update pallet location ke staging jika item memiliki pallet
                if ($item->pallet_id) {
                    Pallet::where('id', $item->pallet_id)->update([
                        'warehouse_id' => $warehouseId,
                        'location_id' => $stagingLocation->id,
                    ]);
                }

                // Catat stock di staging location dengan status QUARANTINE / Pending Putaway
                $balance = InventoryBalance::firstOrNew([
                    'warehouse_id' => $warehouseId,
                    'bin_id' => $stagingLocation->id,
                    'product_id' => $item->product_id,
                    'lot_id' => $item->lot_id,
                    'pallet_id' => $item->pallet_id,
                    'inventory_status' => InventoryStatus::QUARANTINE,
                ]);

                $balance->quantity = (float) $balance->quantity + $acceptedQty;
                $balance->last_movement_at = now();
                $balance->save();

                // Catat ledger StockMovement (Inbound Receipt)
                StockMovement::create([
                    'movement_number' => 'SM-' . strtoupper(uniqid()),
                    'movement_date' => now(),
                    'warehouse_id' => $warehouseId,
                    'bin_id' => $stagingLocation->id,
                    'product_id' => $item->product_id,
                    'lot_id' => $item->lot_id,
                    'pallet_id' => $item->pallet_id,
                    'movement_type' => StockMovementType::RECEIPT,
                    'direction' => MovementDirection::IN,
                    'quantity' => $acceptedQty,
                    'balance_after' => $balance->quantity,
                    'reference_type' => GoodsReceipt::class,
                    'reference_id' => $goodsReceipt->id,
                    'grn_id' => $goodsReceipt->id,
                    'performed_by' => $userId,
                    'notes' => 'Received at staging location via GRN ' . $goodsReceipt->document_number,
                    'created_at' => now(),
                ]);

                // Buat generic Putaway Task
                PutawayTask::create([
                    'warehouse_id' => $warehouseId,
                    'goods_receipt_id' => $goodsReceipt->id,
                    'goods_receipt_item_id' => $item->id,
                    'source_type' => GoodsReceipt::class,
                    'source_id' => $goodsReceipt->id,
                    'product_id' => $item->product_id,
                    'lot_id' => $item->lot_id,
                    'pallet_id' => $item->pallet_id,
                    'source_bin_id' => $stagingLocation->id,
                    'destination_bin_id' => $defaultStorageBin->id,
                    'qty' => $acceptedQty,
                    'status' => PutawayStatus::PENDING,
                ]);
            }

            $goodsReceipt->update([
                'status' => DocumentStatus::APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            return $goodsReceipt;
        });
    }

    /**
     * Konfirmasi penerimaan Production Receipt.
     * Menggunakan flow generic yang sama persis dengan GRN.
     */
    public function confirmProductionReceipt(ProductionReceipt $receipt, ?int $userId = null): ProductionReceipt
    {
        return DB::transaction(function () use ($receipt, $userId) {
            $receipt->refresh();

            // Idempotency check
            if ($receipt->putawayTasks()->exists() && $receipt->status === DocumentStatus::APPROVED) {
                return $receipt;
            }

            $userId = $userId ?? Auth::id() ?? $receipt->received_by;
            $warehouseId = $receipt->warehouse_id;
            $stagingLocation = $this->getOrCreateStagingLocation($warehouseId);
            $defaultStorageBin = $this->getDefaultStorageBin($warehouseId);

            foreach ($receipt->items as $item) {
                $receivedQty = (float) $item->received_qty;
                if ($receivedQty <= 0) {
                    continue;
                }

                $existingTask = PutawayTask::where('production_receipt_item_id', $item->id)->first();
                if ($existingTask) {
                    continue;
                }

                // Update pallet location ke staging jika item memiliki pallet
                if ($item->pallet_id) {
                    Pallet::where('id', $item->pallet_id)->update([
                        'warehouse_id' => $warehouseId,
                        'location_id' => $stagingLocation->id,
                    ]);
                }

                // Catat stock di staging location dengan status QUARANTINE / Pending Putaway
                $balance = InventoryBalance::firstOrNew([
                    'warehouse_id' => $warehouseId,
                    'bin_id' => $stagingLocation->id,
                    'product_id' => $item->product_id,
                    'lot_id' => $item->lot_id,
                    'pallet_id' => $item->pallet_id,
                    'inventory_status' => InventoryStatus::QUARANTINE,
                ]);

                $balance->quantity = (float) $balance->quantity + $receivedQty;
                $balance->last_movement_at = now();
                $balance->save();

                // Catat ledger StockMovement (Production Inbound)
                StockMovement::create([
                    'movement_number' => 'SM-' . strtoupper(uniqid()),
                    'movement_date' => now(),
                    'warehouse_id' => $warehouseId,
                    'bin_id' => $stagingLocation->id,
                    'product_id' => $item->product_id,
                    'lot_id' => $item->lot_id,
                    'pallet_id' => $item->pallet_id,
                    'movement_type' => StockMovementType::PRODUCTION_RECEIPT,
                    'direction' => MovementDirection::IN,
                    'quantity' => $receivedQty,
                    'balance_after' => $balance->quantity,
                    'reference_type' => ProductionReceipt::class,
                    'reference_id' => $receipt->id,
                    'production_receipt_id' => $receipt->id,
                    'performed_by' => $userId,
                    'notes' => 'Received from production at staging via PR ' . $receipt->document_number,
                    'created_at' => now(),
                ]);

                // Gunakan destination_bin_id dari item jika diisi, jika tidak gunakan defaultStorageBin
                $destinationBinId = $item->destination_bin_id ?: $defaultStorageBin->id;

                // Buat generic Putaway Task
                PutawayTask::create([
                    'warehouse_id' => $warehouseId,
                    'production_receipt_id' => $receipt->id,
                    'production_receipt_item_id' => $item->id,
                    'source_type' => ProductionReceipt::class,
                    'source_id' => $receipt->id,
                    'product_id' => $item->product_id,
                    'lot_id' => $item->lot_id,
                    'pallet_id' => $item->pallet_id,
                    'source_bin_id' => $stagingLocation->id,
                    'destination_bin_id' => $destinationBinId,
                    'qty' => $receivedQty,
                    'status' => PutawayStatus::PENDING,
                ]);
            }

            $receipt->update([
                'status' => DocumentStatus::APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            return $receipt;
        });
    }
}
