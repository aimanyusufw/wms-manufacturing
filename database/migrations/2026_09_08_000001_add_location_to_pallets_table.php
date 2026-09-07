<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pallets', function (Blueprint $table): void {
            $table->foreignId('warehouse_id')->nullable()->after('pallet_code')->constrained('warehouses')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->after('warehouse_id')->constrained('locations')->nullOnDelete();

            $table->index('warehouse_id');
            $table->index('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('pallets', function (Blueprint $table): void {
            $table->dropForeign(['location_id']);
            $table->dropForeign(['warehouse_id']);
            $table->dropColumn(['warehouse_id', 'location_id']);
        });
    }
};
