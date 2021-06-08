<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelegramIdColumnToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::beginTransaction();

        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('telegram_id')->nullable()->after('client_id');
        });

        $this->fillTelegramId();

        \Illuminate\Support\Facades\DB::commit();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('telegram_id');
        });
    }

    private function fillTelegramId(): void
    {
        \Illuminate\Support\Facades\DB::table('drivers')
            ->leftJoin('clients', 'drivers.client_id', '=', 'clients.id')
            ->update([
                'drivers.telegram_id' => \Illuminate\Support\Facades\DB::raw('clients.telegram_id')
            ]);
    }
}
