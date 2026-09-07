<?php

use App\Enums\DocumentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_receipts', function (Blueprint $table): void {
            $table->id();
            $table->string('document_number', 100)->unique();
            $table->foreignId('production_order_id')->nullable();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->timestamp('receipt_date');
            $table->enum('status', array_column(DocumentStatus::cases(), 'value'))
                ->default(DocumentStatus::DRAFT->value);
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index('document_number');
            $table->index('production_order_id');
            $table->index('warehouse_id');
            $table->index('receipt_date');
            $table->index('status');
        });

        Schema::create('production_receipt_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('production_receipt_id')->constrained('production_receipts')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('uom_id')->constrained('uoms')->restrictOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->foreignId('pallet_id')->nullable()->constrained('pallets')->nullOnDelete();
            $table->foreignId('destination_bin_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('received_qty', 18, 6);
            $table->decimal('rejected_qty', 18, 6)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index('production_receipt_id');
            $table->index('product_id');
            $table->index('lot_id');
            $table->index('pallet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_receipt_items');
        Schema::dropIfExists('production_receipts');
    }
};
