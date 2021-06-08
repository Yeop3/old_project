<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('seller_id');

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('product_type_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();

            $table->unsignedBigInteger('number');
            $table->bigInteger('commission_value');
            $table->tinyInteger('commission_type');
            $table->string('address')->nullable();
            $table->string('image_src');
            $table->string('video_src')->nullable();

            $table->string('coordinates');

            $table->tinyInteger('status');

            $table->timestamp('booked_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('driver_id')
                ->on('drivers')
                ->references('id')
                ->onDelete('set null');

            $table->foreign('product_type_id')
                ->on('product_types')
                ->references('id')
                ->onDelete('set null');

            $table->foreign('location_id')
                ->on('locations')
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
        Schema::dropIfExists('products');
    }
}
