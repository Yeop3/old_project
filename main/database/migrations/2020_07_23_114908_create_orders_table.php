<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');

            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('discount_id')->nullable();

            $table->unsignedBigInteger('wallet_id');
            $table->string('wallet_type');
            $table->tinyInteger('payment_method');

            $table->unsignedBigInteger('source_id');
            $table->string('source_type');

            $table->tinyInteger('status');

            $table->bigInteger('unpaid_amount');
            $table->bigInteger('price');
            $table->bigInteger('total_price');
            $table->bigInteger('commission');
            $table->bigInteger('discount_amount');
            $table->decimal('crypto_uah_rate', 12)->nullable();

            $table->string('bitaps_payment_address')->nullable();
            $table->string('bitaps_payment_code')->nullable();
            $table->string('bitaps_invoice')->nullable();

            $table->boolean('found')->nullable();
            $table->boolean('not_found')->nullable();

            $table->unsignedSmallInteger('rating')->nullable();
            $table->text('client_comment')->nullable();

            $table->string('name')->nullable();
            $table->string('packing')->nullable();
            $table->string('unit')->nullable();

            $table->timestamp('canceled_at')->nullable();
            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('client_id')
                ->on('sellers')
                ->references('id')
                ->onDelete('set null');

            $table->foreign('discount_id')
                ->on('discounts')
                ->references('id')
                ->onDelete('set null');

            $table->foreign('product_id')
                ->on('products')
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
        Schema::dropIfExists('orders');
    }
}
