<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEasyPayTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('easy_pay_transactions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('easy_pay_wallet_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('shift_id');
            $table->string('transaction_id')->nullable();
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('easy_pay_wallet_id')
                ->on('easy_pay_wallets')
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
        Schema::dropIfExists('easy_pay_transactions');
    }
}
