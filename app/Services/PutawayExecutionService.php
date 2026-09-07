<?php

namespace App\Services;

use App\Enums\InventoryStatus;
use App\Enums\MovementDirection;
use App\Enums\PutawayStatus;
use App\Enums\StockMovementType;
use App\Models\InventoryBalance;
use App\Models\Location;
use App\Models\Pallet;
use App\Models\PutawayTask;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PutawayExecutionService
{
    /**
     * Assign operator to putaway task.
     */
    public function assignTask(PutawayTask $task, int $userId): PutawayTask
    {
        if ($task->status === PutawayStatus::COMPLETED || $task->status === PutawayStatus::CANCELLED) {
            throw ValidationException::withMessages([
                'status' => 'Cannot assign a completed or cancelled task.',
            ]);
        }

        $task->update([
            'assigned_to' => $userId,
            'status' => PutawayStatus::ASSIGNED,
        ]);

        return $task;
    }

    /**
     * Start execution of putaway task (in_progress).
     */
    public function startTask(PutawayTask $task): PutawayTask
    {
        if ($task->status === PutawayStatus::COMPLETED || $task->status === PutawayStatus::CANCELLED) {
            throw ValidationException::withMessages([
                'status' => 'Cannot start a completed or cancelled task.',
            ]);
        }

        $task->update([
            'status' => PutawayStatus::IN_PROGRESS,
            'assigned_to' => $task->assigned_to ?? Auth::id(),
        ]);

        return $task;
    }

    /**
     * Complete putaway task:
     * - Validate destination location
     * - Move stock from staging (QUARANTINE) to destination (AVAILABLE)
     * - Update pallet location to destination
     * - Record StockMovement ledger
     * - Mark PutawayTask as completed
     */
    public function completeTask(PutawayTask $task, ?int $destinationBinId = null, ?int $operatorId = null): PutawayTask
    {
        return DB::transaction(function () use ($task, $destinationBinId, $operatorId) {
            $task->refresh();

            if ($task->status === PutawayStatus::COMPLETED) {
                throw ValidationException::withMessages([
                    'status' => 'Putaway task is already completed.',
                ]);
            }

            if ($task->status === PutawayStatus::CANCELLED) {
                throw ValidationException::withMessages([
                    'status' => 'Cannot complete a cancelled task.',
                ]);
            }

            $operatorId = $operatorId ?? Auth::id() ?? $task->assigned_to;
            $destinationId = $destinationBinId ?? $task->destination_bin_id;

            // Validasi destination location
            $destinationBin = Location::find($destinationId);
            if (! $destinationBin || ! $destinationBin->is_active || ! $destinationBin->is_putaway_allowed) {
                throw ValidationException::withMessages([
                    'destination_bin_id' => 'Destination bin is invalid, inactive, or not allowed for putaway.',
                ]);
            }

            if ($destinationBin->warehouse_id !== $task->warehouse_id) {
                throw ValidationException::withMessages([
                    'destination_bin_id' => 'Destination bin does not belong to the same warehouse.',
                ]);
            }

            $qty = (float) $task->qty;
            $sourceBinId = $task->source_bin_id;

            // 1. Kurangi stock dari source staging location
            if ($sourceBinId) {
                $sourceBalance = InventoryBalance::where([
                    'warehouse_id' => $task->warehouse_id,
                    'bin_id' => $sourceBinId,
                    'product_id' => $task->product_id,
                    'lot_id' => $task->lot_id,
                    'pallet_id' => $task->pallet_id,
                    'inventory_status' => InventoryStatus::QUARANTINE,
                ])->lockForUpdate()->first();

                if ($sourceBalance) {
                    $sourceBalance->quantity = max(0, (float) $sourceBalance->quantity - $qty);
                    $sourceBalance->last_movement_at = now();
                    $sourceBalance->save();

                    // Movement OUT dari staging
                    StockMovement::create([
                        'movement_number' => 'SM-' . strtoupper(uniqid()),
                        'movement_date' => now(),
                        'warehouse_id' => $task->warehouse_id,
                        'bin_id' => $sourceBinId,
                        'product_id' => $task->product_id,
                        'lot_id' => $task->lot_id,
                        'pallet_id' => $task->pallet_id,
                        'movement_type' => StockMovementType::TRANSFER_OUT,
                        'direction' => MovementDirection::OUT,
                        'quantity' => $qty,
                        'balance_after' => $sourceBalance->quantity,
                        'reference_type' => PutawayTask::class,
                        'reference_id' => $task->id,
                        'grn_id' => $task->goods_receipt_id,
                        'production_receipt_id' => $task->production_receipt_id,
                        'performed_by' => $operatorId,
                        'notes' => 'Putaway out from staging to bin ' . $destinationBin->code,
                        'created_at' => now(),
                    ]);
                }
            }

            // 2. Tambah stock ke destination bin dengan status AVAILABLE
            $destBalance = InventoryBalance::firstOrNew([
                'warehouse_id' => $task->warehouse_id,
                'bin_id' => $destinationBin->id,
                'product_id' => $task->product_id,
                'lot_id' => $task->lot_id,
                'pallet_id' => $task->pallet_id,
                'inventory_status' => InventoryStatus::AVAILABLE,
            ]);

            $destBalance->quantity = (float) $destBalance->quantity + $qty;
            $destBalance->last_movement_at = now();
            $destBalance->save();

            // Movement IN ke storage bin
            StockMovement::create([
                'movement_number' => 'SM-' . strtoupper(uniqid()),
                'movement_date' => now(),
                'warehouse_id' => $task->warehouse_id,
                'bin_id' => $destinationBin->id,
                'product_id' => $task->product_id,
                'lot_id' => $task->lot_id,
                'pallet_id' => $task->pallet_id,
                'movement_type' => StockMovementType::TRANSFER_IN,
                'direction' => MovementDirection::IN,
                'quantity' => $qty,
                'balance_after' => $destBalance->quantity,
                'reference_type' => PutawayTask::class,
                'reference_id' => $task->id,
                'grn_id' => $task->goods_receipt_id,
                'production_receipt_id' => $task->production_receipt_id,
                'performed_by' => $operatorId,
                'notes' => 'Putaway completed into storage bin ' . $destinationBin->code,
                'created_at' => now(),
            ]);

            // 3. Update pallet location ke destination bin jika ada
            if ($task->pallet_id) {
                Pallet::where('id', $task->pallet_id)->update([
                    'warehouse_id' => $task->warehouse_id,
                    'location_id' => $destinationBin->id,
                ]);
            }

            // 4. Update task completion
            $task->update([
                'destination_bin_id' => $destinationBin->id,
                'status' => PutawayStatus::COMPLETED,
                'completed_by' => $operatorId,
                'completed_at' => now(),
            ]);

            return $task;
        });
    }
}
