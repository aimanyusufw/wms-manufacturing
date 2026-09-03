<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approval_step_instance_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->text('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['approval_id']);
            $table->index(['approval_step_instance_id']);
            $table->index(['user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_actions');
    }
};
