<?php

use App\Enums\InventoryStatus;
use App\Enums\MovementDirection;
use App\Enums\StockMovementType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_balances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('bin_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->foreignId('pallet_id')->nullable()->constrained('pallets')->nullOnDelete();
            $table->string('inventory_status', 50)->default(InventoryStatus::AVAILABLE->value);
            $table->decimal('quantity', 18, 6)->default(0);
            $table->decimal('reserved_quantity', 18, 6)->default(0);
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['bin_id', 'product_id', 'lot_id', 'pallet_id', 'inventory_status'],
                'inv_balance_unique'
            );
            $table->index('warehouse_id');
            $table->index('bin_id');
            $table->index('product_id');
            $table->index('lot_id');
            $table->index('pallet_id');
            $table->index('inventory_status');
            $table->index(['product_id', 'warehouse_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->string('movement_number', 100)->unique();
            $table->timestamp('movement_date');

            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('bin_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->foreignId('pallet_id')->nullable()->constrained('pallets')->nullOnDelete();

            $table->string('movement_type', 50);
            $table->string('direction', 10);

            $table->decimal('quantity', 18, 6);
            $table->decimal('balance_after', 18, 6);

            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('grn_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->foreignId('production_receipt_id')->nullable()->constrained('production_receipts')->nullOnDelete();

            $table->foreignId('performed_by')->constrained('users')->restrictOnDelete();
            $table->text('notes')->nullable();

            $table->timestamp('created_at');

            $table->index('movement_number');
            $table->index('movement_date');
            $table->index('warehouse_id');
            $table->index('bin_id');
            $table->index('product_id');
            $table->index('lot_id');
            $table->index('pallet_id');
            $table->index('movement_type');
            $table->index('direction');
            $table->index(['reference_type', 'reference_id']);
            $table->index(['product_id', 'warehouse_id', 'movement_date'], 'sm_prod_wh_date_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_balances');
    }
};
