<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspection_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('quality_inspection_id')->constrained('quality_inspections')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('lot_id')->nullable()->constrained('lots')->nullOnDelete();
            $table->decimal('inspected_qty', 18, 6);
            $table->decimal('passed_qty', 18, 6)->default(0);
            $table->decimal('failed_qty', 18, 6)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index('quality_inspection_id');
            $table->index('product_id');
            $table->index('lot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_inspection_items');
    }
};
