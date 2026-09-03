<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_delegations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_step_instance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('reason')->nullable();
            $table->timestamp('delegated_at');
            $table->timestamps();

            $table->unique(['approval_step_instance_id', 'from_user_id'], 'delegation_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_delegations');
    }
};
