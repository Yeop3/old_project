<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationDriver extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('location_driver', function (Blueprint $table) {
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('driver_id');

            $table->primary(['location_id', 'driver_id']);

            $table->foreign('location_id')
                ->on('locations')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('driver_id')
                ->on('drivers')
                ->references('id')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('location_driver');
    }
}
