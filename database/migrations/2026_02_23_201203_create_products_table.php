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
            $table->string('name')->nullable();
            $table->index('name');
            $table->string('sku')->nullable()->unique();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->decimal('sell_price', 12, 2)->nullable();
            $table->integer('opening_stock')->nullable();
            $table->integer('current_stock')->nullable();
            $table->timestamps();
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
