<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentMethodsColumnToProductTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        Schema::table('product_types', function (Blueprint $table) {
            $table->json('payment_methods')->nullable()->after('priority');
        });

        \Illuminate\Support\Facades\DB::table('product_types')->update([
            'payment_methods' => json_encode(
                \App\Services\Wallet\VO\PaymentMethod::getInitTypesForProductTypes(),
                JSON_THROW_ON_ERROR
            ),
        ]);

        \Illuminate\Support\Facades\DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn('payment_methods');
        });
    }
}
