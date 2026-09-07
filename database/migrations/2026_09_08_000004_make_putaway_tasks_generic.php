<?php

use App\Enums\PutawayStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('putaway_tasks', function (Blueprint $table): void {
            $table->foreignId('warehouse_id')->nullable()->after('id')->constrained('warehouses')->restrictOnDelete();

            $table->string('source_type', 100)->nullable()->after('goods_receipt_item_id');
            $table->unsignedBigInteger('source_id')->nullable()->after('source_type');

            $table->foreignId('production_receipt_id')->nullable()->after('source_id')->constrained('production_receipts')->nullOnDelete();
            $table->foreignId('production_receipt_item_id')->nullable()->after('production_receipt_id')->constrained('production_receipt_items')->nullOnDelete();

            $table->index(['source_type', 'source_id']);
            $table->index('production_receipt_id');
            $table->index('production_receipt_item_id');
            $table->index('warehouse_id');
        });

        // Make goods_receipt_id & goods_receipt_item_id nullable & adjust status type/values
        $driver = DB::getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE putaway_tasks MODIFY goods_receipt_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE putaway_tasks MODIFY goods_receipt_item_id BIGINT UNSIGNED NULL');
            DB::statement("ALTER TABLE putaway_tasks MODIFY status VARCHAR(50) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        Schema::table('putaway_tasks', function (Blueprint $table): void {
            $table->dropIndex(['source_type', 'source_id']);
            $table->dropIndex(['production_receipt_id']);
            $table->dropIndex(['production_receipt_item_id']);
            $table->dropIndex(['warehouse_id']);

            $table->dropForeign(['production_receipt_item_id']);
            $table->dropForeign(['production_receipt_id']);
            $table->dropForeign(['warehouse_id']);

            $table->dropColumn([
                'warehouse_id',
                'source_type',
                'source_id',
                'production_receipt_id',
                'production_receipt_item_id',
            ]);
        });
    }
};
