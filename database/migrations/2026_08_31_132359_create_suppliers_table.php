<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string("code", 50)->unique();
            $table->string("name", 200);
            $table->string("contact_person", 150)->nullable();
            $table->string("phone", 50)->nullable();
            $table->string("email", 50)->nullable();
            $table->string("country", 100)->nullable();
            $table->text("address")->nullable();
            $table->string("tax_number", 100)->nullable();
            $table->boolean("is_active")->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('code');
            $table->index('name');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
