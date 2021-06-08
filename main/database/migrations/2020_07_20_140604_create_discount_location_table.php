<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('discount_location', function (Blueprint $table) {
            $table->unsignedBigInteger('discount_id');
            $table->unsignedBigInteger('location_id');

            $table->primary(['discount_id', 'location_id']);

            $table->foreign('discount_id')
                ->on('discounts')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('location_id')
                ->on('locations')
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
        Schema::dropIfExists('discount_location');
    }
}
