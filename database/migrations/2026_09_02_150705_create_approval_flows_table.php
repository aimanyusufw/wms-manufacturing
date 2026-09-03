<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tenancyEnabled = config('filament-approval.multi_tenancy.enabled', false);
        $tenantColumn = config('filament-approval.multi_tenancy.column', 'company_id');

        Schema::create('approval_flows', function (Blueprint $table) use ($tenancyEnabled, $tenantColumn) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('approvable_type')->nullable();

            if ($tenancyEnabled) {
                $table->unsignedBigInteger($tenantColumn)->nullable()->index();
            }

            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            if ($tenancyEnabled) {
                $table->index(['approvable_type', $tenantColumn, 'is_active']);
            } else {
                $table->index(['approvable_type', 'is_active']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_flows');
    }
};
