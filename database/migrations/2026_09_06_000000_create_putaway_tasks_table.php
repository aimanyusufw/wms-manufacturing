<?php

use App\Enums\DocumentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('putaway_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained('goods_receipts')->restrictOnDelete();
            $table->foreignId('goods_receipt_item_id')->constrained('goods_receipt_items')->restrictOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->foreignId('pallet_id')->nullable()->constrained('pallets')->nullOnDelete();
            $table->foreignId('source_bin_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('destination_bin_id')->constrained('locations')->restrictOnDelete();
            $table->decimal('qty', 18, 6);
            $table->enum('status', array_column(DocumentStatus::cases(), 'value'))
                ->default(DocumentStatus::DRAFT->value);
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index('goods_receipt_id');
            $table->index('goods_receipt_item_id');
            $table->index('product_id');
            $table->index('destination_bin_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('putaway_tasks');
    }
};
