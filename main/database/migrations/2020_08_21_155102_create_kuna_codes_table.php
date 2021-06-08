<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKunaCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('kuna_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('kuna_account_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('shift_id');

            $table->string('code')->nullable();
            $table->string('internal_id')->nullable();
            $table->string('sn')->nullable();

            $table->integer('amount');

            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('shift_id')
                ->on('shifts')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('kuna_account_id')
                ->on('kuna_accounts')
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
        Schema::dropIfExists('kuna_codes');
    }
}
