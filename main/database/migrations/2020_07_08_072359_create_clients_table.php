<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('number');

            $table->string('name')->nullable();
            $table->string('username')->nullable();

            $table->unsignedBigInteger('telegram_id')->nullable();
            $table->json('info')->nullable();

            $table->unsignedBigInteger('source_id');
            $table->string('source_type');

            $table->string('note')->nullable();
            $table->decimal('discount_value', 5)->default(0);
            $table->integer('discount_priority')->default(100);

            $table->timestamp('ban_expires_at')->nullable();
            $table->boolean('in_black_list')->default(false);

            $table->json('pre_order')->nullable();

            $table->timestamps();
            $table->timestamp('visited_at')->nullable();
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
        Schema::dropIfExists('clients');
    }
}
