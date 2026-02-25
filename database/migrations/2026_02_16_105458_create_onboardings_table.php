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
        Schema::create('onboardings', function (Blueprint $table) {
           $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Business type (product, service, both, freelance)
            $table->string('business_type')->nullable();

            // Boolean answers
            $table->boolean('has_stock')->nullable();
            $table->boolean('has_appointments')->nullable();
            $table->boolean('has_staff')->nullable();

            // Timestamps
            $table->timestamps();

            // Unique index → one onboarding per user
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('onboardings');
    }
};
