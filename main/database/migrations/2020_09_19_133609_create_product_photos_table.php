<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPhotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('product_photos', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('number');
            $table->unsignedBigInteger('seller_id');

            $table->unsignedBigInteger('product_id');
            $table->string('src');
            $table->timestamps();

            $table->foreign('product_id')
                ->on('products')
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
        Schema::dropIfExists('product_photos');
    }
}
