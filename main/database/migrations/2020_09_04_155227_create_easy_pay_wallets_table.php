<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEasyPayWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('easy_pay_wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('seller_id');

            $table->string('name');
            $table->unsignedBigInteger('proxy_id')->nullable();

            $table->string('phone');
            $table->string('password');

            $table->boolean('wrong_creadentials')->default(0);
            $table->boolean('active')->default(1);

            $table->unsignedBigInteger('wallet_number');
            $table->unsignedBigInteger('external_id');
            $table->unsignedBigInteger('instrument_id');

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
        Schema::dropIfExists('easy_pay_wallets');
    }
}
