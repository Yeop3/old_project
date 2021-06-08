<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQiwiManualPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('qiwi_manual_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('qiwi_manual_wallet_id');

            $table->unsignedBigInteger('number');
            $table->integer('amount');
            $table->string('client_wallet')->nullable();
            $table->text('comment')->nullable();

            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('qiwi_manual_wallet_id')
                ->on('qiwi_manual_wallets')
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
        Schema::dropIfExists('qiwi_manual_payments');
    }
}
