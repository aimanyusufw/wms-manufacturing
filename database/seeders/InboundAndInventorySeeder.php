<?php

namespace Database\Seeders;

use App\Enums\DocumentStatus;
use App\Enums\PurchaseOrderStatus;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\QualityInspection;
use App\Models\QualityInspectionItem;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InboundReceivingService;
use App\Services\PutawayExecutionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InboundAndInventorySeeder extends Seeder
{
    /**
     * Seed transactional inbound, purchase orders, receipts, quality inspections, and putaway tasks.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::first() ?? User::factory()->create([
                'name' => 'WMS Admin',
                'email' => 'admin@example.com',
            ]);

            $warehouse = Warehouse::first() ?? Warehouse::factory()->create();
            $products = Product::all();
            if ($products->isEmpty()) {
                $products = Product::factory()->count(5)->create();
            }

            $suppliers = Supplier::all();
            if ($suppliers->isEmpty()) {
                $suppliers = Supplier::factory()->count(2)->create();
            }

            $inboundService = app(InboundReceivingService::class);
            $putawayService = app(PutawayExecutionService::class);

            // 1. Purchase Orders with Items
            $purchaseOrders = PurchaseOrder::factory()->count(2)->create([
                'warehouse_id' => $warehouse->id,
                'supplier_id' => $suppliers->random()->id,
                'created_by' => $user->id,
                'approved_by' => $user->id,
                'status' => PurchaseOrderStatus::APPROVED,
            ]);

            foreach ($purchaseOrders as $po) {
                foreach ($products->take(3) as $product) {
                    PurchaseOrderItem::factory()->create([
                        'purchase_order_id' => $po->id,
                        'product_id' => $product->id,
                        'uom_id' => $product->base_uom_id,
                        'quantity' => 50,
                    ]);
                }
            }

            // 2. Goods Receipts (GRN) from PO & Confirming to Putaway
            $poToReceive = $purchaseOrders->first();
            $grn = GoodsReceipt::factory()->create([
                'purchase_order_id' => $poToReceive->id,
                'supplier_id' => $poToReceive->supplier_id,
                'warehouse_id' => $warehouse->id,
                'received_by' => $user->id,
                'approved_by' => $user->id,
                'status' => DocumentStatus::DRAFT,
            ]);

            foreach ($poToReceive->items as $poItem) {
                $pallet = Pallet::where('warehouse_id', $warehouse->id)->whereNull('location_id')->first()
                    ?? Pallet::factory()->create(['warehouse_id' => $warehouse->id]);

                $lot = Lot::factory()->create([
                    'product_id' => $poItem->product_id,
                ]);

                GoodsReceiptItem::factory()->create([
                    'goods_receipt_id' => $grn->id,
                    'purchase_order_item_id' => $poItem->id,
                    'product_id' => $poItem->product_id,
                    'uom_id' => $poItem->uom_id,
                    'lot_id' => $lot->id,
                    'pallet_id' => $pallet->id,
                    'received_qty' => 50,
                    'accepted_qty' => 50,
                    'rejected_qty' => 0,
                ]);
            }

            // Konfirmasi GRN -> Buat Putaway Tasks & Staging Stock secara atomik
            $inboundService->confirmGoodsReceipt($grn, $user->id);

            // 3. Production Receipt & Confirming to Putaway
            $prodReceipt = ProductionReceipt::factory()->create([
                'warehouse_id' => $warehouse->id,
                'received_by' => $user->id,
                'approved_by' => $user->id,
                'status' => DocumentStatus::DRAFT,
            ]);

            $storageBin = Location::where('warehouse_id', $warehouse->id)
                ->where('is_putaway_allowed', true)
                ->first() ?? Location::factory()->create(['warehouse_id' => $warehouse->id]);

            foreach ($products->take(2) as $product) {
                $pallet = Pallet::where('warehouse_id', $warehouse->id)->whereNull('location_id')->first()
                    ?? Pallet::factory()->create(['warehouse_id' => $warehouse->id]);

                $lot = Lot::factory()->create([
                    'product_id' => $product->id,
                ]);

                ProductionReceiptItem::factory()->create([
                    'production_receipt_id' => $prodReceipt->id,
                    'product_id' => $product->id,
                    'uom_id' => $product->base_uom_id,
                    'lot_id' => $lot->id,
                    'pallet_id' => $pallet->id,
                    'destination_bin_id' => $storageBin->id,
                    'received_qty' => 30,
                    'rejected_qty' => 0,
                ]);
            }

            // Konfirmasi Production Receipt
            $inboundService->confirmProductionReceipt($prodReceipt, $user->id);

            // 4. Quality Inspection (Sample Passed)
            $inspection = QualityInspection::factory()->create([
                'goods_receipt_id' => $grn->id,
                'inspected_by' => $user->id,
                'approved_by' => $user->id,
            ]);

            foreach ($grn->items as $grnItem) {
                QualityInspectionItem::factory()->create([
                    'quality_inspection_id' => $inspection->id,
                    'product_id' => $grnItem->product_id,
                    'lot_id' => $grnItem->lot_id,
                    'inspected_qty' => $grnItem->accepted_qty,
                    'passed_qty' => $grnItem->accepted_qty,
                    'failed_qty' => 0,
                ]);
            }

            // 5. Complete satu Putaway Task sebagai contoh siklus utuh
            $taskToComplete = $grn->putawayTasks()->first();
            if ($taskToComplete && $storageBin) {
                $putawayService->assignTask($taskToComplete, $user->id);
                $putawayService->startTask($taskToComplete);
                $putawayService->completeTask($taskToComplete, $storageBin->id, $user->id);
            }
        });
    }
}
