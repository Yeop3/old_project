<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotDriverTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bot_driver', function (Blueprint $table) {
            $table->unsignedBigInteger('bot_id');
            $table->unsignedBigInteger('driver_id');

            $table->primary(['bot_id', 'driver_id']);

            $table->foreign('bot_id')
                ->on('bots')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('driver_id')
                ->on('drivers')
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
        Schema::dropIfExists('bot_driver');
    }
}
