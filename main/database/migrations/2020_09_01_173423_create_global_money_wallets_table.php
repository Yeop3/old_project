<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalMoneyWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('global_money_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('seller_id');

            $table->string('name');
            $table->unsignedBigInteger('proxy_id')->nullable();

            $table->string('login');
            $table->string('password');
            $table->string('type');
            $table->string('wallet_number');

            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('wrong_credentials')->default(0);

            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('proxy_id')
                ->on('sellers')
                ->references('id')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('global_money_wallets');
    }
}
