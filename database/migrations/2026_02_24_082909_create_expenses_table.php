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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            
            $table->string('title', 150);
            $table->text('description')->nullable();
            
            $table->decimal('amount', 12, 2);
            
            $table->date('expense_date');
            
            $table->enum('category', [
                'rent',
                'electricity',
                'purchase_stock',
                'salary',
                'transport',
                'marketing',
                'maintenance',
                'other'
            ])->default('other');
            
            $table->string('payment_mode', 50)->nullable(); // cash, upi, bank, card, etc.
            
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'expense_date']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
