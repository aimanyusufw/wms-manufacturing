<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\InventoryStatus;
use App\Enums\LocationType;
use App\Enums\PutawayStatus;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Location;
use App\Models\Lot;
use App\Models\Pallet;
use App\Models\Product;
use App\Models\ProductionReceipt;
use App\Models\ProductionReceiptItem;
use App\Models\PutawayTask;
use App\Models\Supplier;
use App\Models\Uom;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\InboundReceivingService;
use App\Services\PutawayExecutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InboundAndPutawayTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Warehouse $warehouse;
    protected Location $stagingLocation;
    protected Location $storageBin;
    protected Product $product;
    protected Uom $uom;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['name' => 'Operator']);
        $this->actingAs($this->user);

        $this->warehouse = Warehouse::create([
            'code' => 'WH01',
            'name' => 'Main Warehouse',
            'is_active' => true,
        ]);

        $this->uom = Uom::create([
            'code' => 'PCS',
            'name' => 'Pieces',
        ]);

        $this->product = Product::create([
            'sku' => 'PROD-001',
            'name' => 'Test Product',
            'uom_id' => $this->uom->id,
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'code' => 'SUP-001',
            'name' => 'Test Supplier',
        ]);

        $this->stagingLocation = Location::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'STG-01',
            'name' => 'Staging Area',
            'location_type' => LocationType::Staging,
            'is_pickable' => false,
            'is_putaway_allowed' => false,
            'is_active' => true,
        ]);

        $this->storageBin = Location::create([
            'warehouse_id' => $this->warehouse->id,
            'code' => 'BIN-A1',
            'name' => 'Storage Bin A1',
            'location_type' => LocationType::Bin,
            'is_pickable' => true,
            'is_putaway_allowed' => true,
            'is_active' => true,
        ]);
    }

    public function test_grn_confirmation_creates_stock_quarantine_pallet_and_putaway_task(): void
    {
        $pallet = Pallet::create(['pallet_code' => 'PLT-001']);
        $lot = Lot::create(['product_id' => $this->product->id, 'lot_number' => 'LOT-001']);

        $grn = GoodsReceipt::create([
            'warehouse_id' => $this->warehouse->id,
            'supplier_id' => $this->supplier->id,
            'document_number' => 'GRN-001',
            'receipt_date' => now(),
            'status' => DocumentStatus::DRAFT,
            'received_by' => $this->user->id,
        ]);

        $item = GoodsReceiptItem::create([
            'goods_receipt_id' => $grn->id,
            'product_id' => $this->product->id,
            'uom_id' => $this->uom->id,
            'lot_id' => $lot->id,
            'pallet_id' => $pallet->id,
            'received_qty' => 10,
            'accepted_qty' => 10,
            'rejected_qty' => 0,
        ]);

        $receivingService = app(InboundReceivingService::class);
        $receivingService->confirmGoodsReceipt($grn, $this->user->id);

        $this->assertDatabaseHas('inventory_balances', [
            'warehouse_id' => $this->warehouse->id,
            'bin_id' => $this->stagingLocation->id,
            'product_id' => $this->product->id,
            'lot_id' => $lot->id,
            'pallet_id' => $pallet->id,
            'inventory_status' => InventoryStatus::QUARANTINE->value,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('pallets', [
            'id' => $pallet->id,
            'location_id' => $this->stagingLocation->id,
        ]);

        $this->assertDatabaseHas('putaway_tasks', [
            'warehouse_id' => $this->warehouse->id,
            'goods_receipt_id' => $grn->id,
            'goods_receipt_item_id' => $item->id,
            'product_id' => $this->product->id,
            'lot_id' => $lot->id,
            'pallet_id' => $pallet->id,
            'source_bin_id' => $this->stagingLocation->id,
            'qty' => 10,
            'status' => PutawayStatus::PENDING->value,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'warehouse_id' => $this->warehouse->id,
            'bin_id' => $this->stagingLocation->id,
            'product_id' => $this->product->id,
            'direction' => 'in',
            'quantity' => 10,
        ]);

        // Test Idempotency: calling confirm again must not duplicate tasks
        $receivingService->confirmGoodsReceipt($grn, $this->user->id);
        $this->assertEquals(1, PutawayTask::where('goods_receipt_id', $grn->id)->count());
    }

    public function test_production_receipt_creates_generic_putaway_task(): void
    {
        $pallet = Pallet::create(['pallet_code' => 'PLT-PR-001']);
        $lot = Lot::create(['product_id' => $this->product->id, 'lot_number' => 'LOT-PR-001']);

        $receipt = ProductionReceipt::create([
            'document_number' => 'PR-001',
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now(),
            'status' => DocumentStatus::DRAFT,
            'received_by' => $this->user->id,
        ]);

        $item = ProductionReceiptItem::create([
            'production_receipt_id' => $receipt->id,
            'product_id' => $this->product->id,
            'uom_id' => $this->uom->id,
            'lot_id' => $lot->id,
            'pallet_id' => $pallet->id,
            'destination_bin_id' => $this->storageBin->id,
            'received_qty' => 25,
            'rejected_qty' => 0,
        ]);

        $receivingService = app(InboundReceivingService::class);
        $receivingService->confirmProductionReceipt($receipt, $this->user->id);

        $this->assertDatabaseHas('inventory_balances', [
            'warehouse_id' => $this->warehouse->id,
            'bin_id' => $this->stagingLocation->id,
            'product_id' => $this->product->id,
            'inventory_status' => InventoryStatus::QUARANTINE->value,
            'quantity' => 25,
        ]);

        $this->assertDatabaseHas('putaway_tasks', [
            'warehouse_id' => $this->warehouse->id,
            'production_receipt_id' => $receipt->id,
            'production_receipt_item_id' => $item->id,
            'product_id' => $this->product->id,
            'source_bin_id' => $this->stagingLocation->id,
            'destination_bin_id' => $this->storageBin->id,
            'qty' => 25,
            'status' => PutawayStatus::PENDING->value,
        ]);

        // Test Idempotency
        $receivingService->confirmProductionReceipt($receipt, $this->user->id);
        $this->assertEquals(1, PutawayTask::where('production_receipt_id', $receipt->id)->count());
    }

    public function test_putaway_task_lifecycle_and_completion(): void
    {
        $pallet = Pallet::create(['pallet_code' => 'PLT-PUTAWAY']);
        $lot = Lot::create(['product_id' => $this->product->id, 'lot_number' => 'LOT-PUTAWAY']);

        $receipt = ProductionReceipt::create([
            'document_number' => 'PR-PUTAWAY',
            'warehouse_id' => $this->warehouse->id,
            'receipt_date' => now(),
            'status' => DocumentStatus::DRAFT,
            'received_by' => $this->user->id,
        ]);

        $item = ProductionReceiptItem::create([
            'production_receipt_id' => $receipt->id,
            'product_id' => $this->product->id,
            'uom_id' => $this->uom->id,
            'lot_id' => $lot->id,
            'pallet_id' => $pallet->id,
            'destination_bin_id' => $this->storageBin->id,
            'received_qty' => 15,
            'rejected_qty' => 0,
        ]);

        app(InboundReceivingService::class)->confirmProductionReceipt($receipt, $this->user->id);

        $task = PutawayTask::where('production_receipt_id', $receipt->id)->first();
        $this->assertNotNull($task);

        $putawayService = app(PutawayExecutionService::class);

        // 1. Assign Task
        $putawayService->assignTask($task, $this->user->id);
        $this->assertEquals(PutawayStatus::ASSIGNED, $task->fresh()->status);
        $this->assertEquals($this->user->id, $task->fresh()->assigned_to);

        // 2. Start Task
        $putawayService->startTask($task);
        $this->assertEquals(PutawayStatus::IN_PROGRESS, $task->fresh()->status);

        // 3. Complete Task into storageBin
        $putawayService->completeTask($task, $this->storageBin->id, $this->user->id);
        $this->assertEquals(PutawayStatus::COMPLETED, $task->fresh()->status);

        // Check stock in destination is AVAILABLE
        $this->assertDatabaseHas('inventory_balances', [
            'warehouse_id' => $this->warehouse->id,
            'bin_id' => $this->storageBin->id,
            'product_id' => $this->product->id,
            'inventory_status' => InventoryStatus::AVAILABLE->value,
            'quantity' => 15,
        ]);

        // Pallet location updated to storage bin
        $this->assertDatabaseHas('pallets', [
            'id' => $pallet->id,
            'location_id' => $this->storageBin->id,
        ]);

        // Stock movement ledger recorded
        $this->assertDatabaseHas('stock_movements', [
            'warehouse_id' => $this->warehouse->id,
            'bin_id' => $this->storageBin->id,
            'direction' => 'in',
            'quantity' => 15,
        ]);

        // Cannot complete completed task again
        $this->expectException(ValidationException::class);
        $putawayService->completeTask($task, $this->storageBin->id, $this->user->id);
    }
}
