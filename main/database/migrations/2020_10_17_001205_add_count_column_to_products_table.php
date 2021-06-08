<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountColumnToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('products', 'count')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('count', 10)->nullable()->after('delivery_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasColumn('products', 'count')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('count');
        });
    }
}
