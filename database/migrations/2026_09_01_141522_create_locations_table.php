<?php

use App\Enums\LocationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('warehouse_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('locations')
                ->nullOnDelete();

            $table->string('code', 100);

            $table->string('name');

            $table->enum('location_type', array_column(LocationType::cases(), 'value'));

            $table->unsignedInteger('level')
                ->default(1);

            $table->decimal('max_capacity', 15, 3)
                ->nullable();

            $table->foreignId('capacity_uom')
                ->constrained('uoms')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->nullable();

            $table->boolean('is_pickable')
                ->default(false);

            $table->boolean('is_putaway_allowed')
                ->default(false);

            $table->boolean('is_active')
                ->default(true)
                ->index();

            $table->text('description')
                ->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique([
                'warehouse_id',
                'code',
            ]);

            $table->index([
                'warehouse_id',
                'location_type',
            ]);

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
