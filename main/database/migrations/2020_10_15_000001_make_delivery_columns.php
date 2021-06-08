<?php

use App\Services\ProductType\VO\DeliveryType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MakeDeliveryColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::beginTransaction();

        Schema::table('product_types', function (Blueprint $table) {
            $table->integer('packing')->nullable()->change();
            $table->integer('unit')->nullable()->change();
            $table->tinyInteger('delivery_type')->default(DeliveryType::TREASURE)->after('unit');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('delivery_address')->nullable()->after('status');
            $table->tinyInteger('delivery_type')->default(DeliveryType::TREASURE)->after('status');
            $table->timestamp('delivered_at')->after('booked_at');
            $table->timestamp('delivery_started_at')->after('booked_at');
        });

        DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::beginTransaction();

        Schema::table('product_types', function (Blueprint $table) {
            $table->dropColumn('delivery_type');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('delivery_type', 'delivery_address', 'packing', 'delivered_at', 'delivery_started_at');
        });

        DB::commit();
    }
}
