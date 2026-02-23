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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable()->unique();

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->date('sale_date');

            $table->decimal('sub_total', 12, 2)->nullable();
            $table->decimal('discount', 12, 2)->nullable()->default(0);
            $table->decimal('vat_percent', 5, 2)->nullable()->default(0);
            $table->decimal('vat_amount', 12, 2)->nullable()->default(0);
            $table->decimal('grand_total', 12, 2)->nullable();

            $table->decimal('paid_amount', 12, 2)->nullable()->default(0);
            $table->decimal('due_amount', 12, 2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
