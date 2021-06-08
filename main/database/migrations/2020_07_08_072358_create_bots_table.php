<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');

            $table->string('name');
            $table->string('username');
            $table->string('slug')->unique();
            $table->string('token');
            $table->tinyInteger('messenger');
            $table->tinyInteger('type');

            $table->unsignedBigInteger('logic_id');

            $table->tinyInteger('active');
            $table->tinyInteger('allow_create_clients');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('logic_id')
                ->on('bot_logics')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
}
