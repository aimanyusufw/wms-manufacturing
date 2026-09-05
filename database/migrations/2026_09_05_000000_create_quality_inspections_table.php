<?php

use App\Enums\QcStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_inspections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('goods_receipt_id')->nullable()->constrained('goods_receipts')->nullOnDelete();
            $table->foreignId('production_receipt_id')->nullable();
            $table->string('inspection_number', 100)->unique();
            $table->timestamp('inspection_date');
            $table->enum('status', array_column(QcStatus::cases(), 'value'))
                ->default(QcStatus::PENDING->value);
            $table->foreignId('inspected_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();

            $table->index('inspection_number');
            $table->index('goods_receipt_id');
            $table->index('production_receipt_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_inspections');
    }
};
