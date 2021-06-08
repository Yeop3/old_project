<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalMoneyTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('global_money_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('global_money_wallet_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('shift_id');
            $table->string('transaction_id')->nullable();
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('global_money_wallet_id')
                ->on('global_money_wallets')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('order_id')
                ->on('orders')
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
        Schema::dropIfExists('global_money_transactions');
    }
}
