<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pallets', function (Blueprint $table): void {
            $table->id();
            $table->string('pallet_code', 100)->unique();
            $table->string('pallet_type', 50)->nullable();
            $table->decimal('weight', 18, 6)->nullable();
            $table->string('status', 50)->default('active');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->index('pallet_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pallets');
    }
};
