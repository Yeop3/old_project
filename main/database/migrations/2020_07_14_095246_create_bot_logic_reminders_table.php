<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotLogicRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bot_logic_reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bot_logic_id');
            $table->string('key');
            $table->json('options');
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
        Schema::dropIfExists('bot_logic_reminders');
    }
}
