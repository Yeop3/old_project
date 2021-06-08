<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');

            $table->string('name');
            $table->bigInteger('price');
            $table->bigInteger('commission_value');
            $table->tinyInteger('commission_type');
            $table->integer('packing');
            $table->integer('real_packing');
            $table->tinyInteger('unit');
            $table->integer('priority')->default(0);

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
        Schema::dropIfExists('product_types');
    }
}
