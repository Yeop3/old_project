<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('stokers', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');

            $table->unsignedBigInteger('client_id');

            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('product_type_id');

            $table->unsignedBigInteger('source_id');
            $table->string('source_type');

            $table->timestamps();

            $table->foreign('seller_id')
                ->on('sellers')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('client_id')
                ->on('clients')
                ->references('id');

            $table->foreign('location_id')
                ->on('locations')
                ->references('id');

            $table->foreign('product_type_id')
                ->on('product_types')
                ->references('id');

            $table->foreign('source_id')
                ->on('bots')
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
        Schema::dropIfExists('stokers');
    }
}
