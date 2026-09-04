<?php

use App\Enums\LotStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('lot_number', 100);
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', array_column(LotStatus::cases(), 'value'))
                ->default(LotStatus::ACTIVE->value);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->unique(['product_id', 'lot_number']);
            $table->index('product_id');
            $table->index('lot_number');
            $table->index('status');
            $table->index('expiry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
