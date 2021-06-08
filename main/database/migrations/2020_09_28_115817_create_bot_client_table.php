<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bot_client', function (Blueprint $table) {
            $table->unsignedBigInteger('bot_id');
            $table->unsignedBigInteger('client_id');

            $table->foreign('bot_id')
                ->on('bots')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('client_id')
                ->on('clients')
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
        Schema::dropIfExists('bot_client');
    }
}
