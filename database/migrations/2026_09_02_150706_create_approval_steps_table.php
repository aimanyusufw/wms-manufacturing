<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_flow_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('order');
            $table->string('type')->default('single');
            $table->string('approver_resolver');
            $table->json('approver_config')->nullable();
            $table->unsignedInteger('required_approvals')->default(1);
            $table->unsignedInteger('sla_hours')->nullable();
            $table->string('escalation_action')->nullable();
            $table->json('escalation_config')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['approval_flow_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_steps');
    }
};
