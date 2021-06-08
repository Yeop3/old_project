<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');
            $table->string('name');
            $table->text('description')->nullable();

            $table->decimal('discount_value', 5)->default(0);
            $table->integer('discount_priority')->default(100);

            $table->tinyInteger('active');
            $table->json('payment_methods')->nullable();

            $table->timestamp('date_start')->nullable();
            $table->timestamp('date_end')->nullable();
            $table->integer('client_min_paid_orders_count')->default(0);
            $table->integer('client_min_income')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seller_id')
                ->on('sellers')
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
        Schema::dropIfExists('discounts');
    }
}
