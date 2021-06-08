<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveColumnToProductTypes extends Migration
{
    public function up(): void
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->tinyInteger('active')->default(0)->after('payment_methods');
        });
    }

    public function down(): void
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
}
