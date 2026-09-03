<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_step_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approval_step_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('order');
            $table->string('type');
            $table->string('status')->default('pending');
            $table->unsignedInteger('required_approvals')->default(1);
            $table->unsignedInteger('received_approvals')->default(0);
            $table->json('assigned_approver_ids');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('sla_deadline')->nullable();
            $table->boolean('sla_warning_sent')->default(false);
            $table->boolean('sla_breached')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['approval_id', 'order']);
            $table->index(['status', 'sla_deadline']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_step_instances');
    }
};
