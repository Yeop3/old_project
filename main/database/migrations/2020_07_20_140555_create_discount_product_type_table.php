<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountProductTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('discount_product_type', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('product_type_id');

            $table->primary(['discount_id', 'product_type_id']);

            $table->foreign('discount_id')
                ->on('discounts')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('product_type_id')
                ->on('product_types')
                ->references('id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_product_type');
    }
}
