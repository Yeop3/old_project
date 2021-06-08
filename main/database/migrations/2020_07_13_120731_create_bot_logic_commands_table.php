<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotLogicCommandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bot_logic_commands', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bot_logic_id');
            $table->json('keys');
            $table->timestamps();

            $table->foreign('bot_logic_id')
                ->on('bot_logics')
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
        Schema::dropIfExists('bot_logic_commands');
    }
}
