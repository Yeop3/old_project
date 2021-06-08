<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLimitEasyPayWallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('easy_pay_wallets', function (Blueprint $table) {
            $table->bigInteger('limit')->default(8000 * 100)->after('instrument_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('easy_pay_wallets', function (Blueprint $table) {
            $table->dropColumn(['limit']);
        });
    }
}
