<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDefaultValueActiveToProductTypes extends Migration
{
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        Schema::table('product_types', function (Blueprint $table) {
            $table->integer('active')->default(1)->change();
        });

        \Illuminate\Support\Facades\DB::table('product_types')->update([
            'active' => 1,
        ]);

        \Illuminate\Support\Facades\DB::commit();
    }

    public function down(): void
    {
    }
}
