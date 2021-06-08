<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRestoreDateToEasyPayWallets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('easy_pay_wallets', function (Blueprint $table) {
            $table->dateTime('restore_date')->nullable();
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
            $table->dropColumn(['restore_date']);
        });
    }
}
