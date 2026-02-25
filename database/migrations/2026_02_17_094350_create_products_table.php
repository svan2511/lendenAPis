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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');           

            $table->string('name');
            $table->decimal('price', 12, 2);       // supports up to 999,999,999.99

            // Current quantity in stock (for products)
            // For services → usually 0 or null, but we keep it simple
            
            $table->decimal('quantity', 12, 3)->default(0);

            // 'product' or 'service'
            $table->enum('type', ['product', 'service'])->default('product');

            // For products: 'weight' = sold by kg, 'fixed' = sold by unit/piece
            // For services: almost always 'fixed'
            $table->enum('unit_type', ['weight', 'fixed'])->default('fixed');

            $table->timestamps();
            // Common indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('unit_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
