<?php

use App\Enums\DocumentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('supplier_id')->constrained('suppliers')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->string('document_number', 100)->unique();
            $table->timestamp('receipt_date');
            $table->enum('status', array_column(DocumentStatus::cases(), 'value'))
                ->default(DocumentStatus::DRAFT->value);
            $table->string('delivery_note_number', 100)->nullable();
            $table->foreignId('received_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->index('document_number');
            $table->index('purchase_order_id');
            $table->index('supplier_id');
            $table->index('warehouse_id');
            $table->index('receipt_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipts');
    }
};
