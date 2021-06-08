<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotLogicCommandTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bot_logic_command_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bot_logic_command_id');

            $table->string('key');

            $table->text('content');

            $table->timestamps();

            $table->foreign('bot_logic_command_id')
                ->on('bot_logic_commands')
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
        Schema::dropIfExists('bot_logic_command_templates');
    }
}
