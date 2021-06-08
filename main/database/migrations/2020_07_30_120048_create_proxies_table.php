<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProxiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('proxies', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');

            $table->string('ip');
            $table->unsignedInteger('port')->nullable();

            $table->string('username')->nullable();
            $table->string('password')->nullable();

            $table->string('proxy_type');

            $table->string('note')->nullable();

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
        Schema::dropIfExists('proxies');
    }
}
