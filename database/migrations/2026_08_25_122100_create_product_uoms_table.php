<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_uoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('uom_id')
                ->constrained('uoms')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('conversion_factor', 18, 6);
            $table->boolean('is_purchase_uom')->default(false);
            $table->boolean('is_sales_uom')->default(false);
            $table->timestamps();

            // Indexes
            $table->unique(['product_id', 'uom_id']);
            $table->index('product_id');
            $table->index('uom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_uoms');
    }
};
