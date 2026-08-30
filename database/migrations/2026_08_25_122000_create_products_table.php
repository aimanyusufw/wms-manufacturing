<?php

use App\Enums\ProductType;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('product_categories')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('base_uom_id')
                ->constrained('uoms')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->nullable()->unique();
            $table->string('name', 200);
            $table->enum('product_type', array_column(ProductType::cases(), 'value'));
            $table->text('description')->nullable();
            $table->decimal('min_stock', 18, 6)->default(0);
            $table->decimal('max_stock', 18, 6)->nullable();
            $table->decimal('reorder_point', 18, 6)->nullable();
            $table->integer('shelf_life_days')->nullable();
            $table->boolean('track_lot')->default(false);
            $table->boolean('track_serial')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('sku');
            $table->index('barcode');
            $table->index('category_id');
            $table->index('product_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
