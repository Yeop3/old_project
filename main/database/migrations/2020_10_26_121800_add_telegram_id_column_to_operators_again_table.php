<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTelegramIdColumnToOperatorsAgainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('operators', 'telegram_id')) {
            $this->fillTelegramId();

            return;
        }

        \Illuminate\Support\Facades\DB::beginTransaction();

        Schema::table('operators', function (Blueprint $table) {
            $table->unsignedBigInteger('telegram_id')->nullable()->after('number');
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
        if (!Schema::hasColumn('operators', 'telegram_id')) {
            return;
        }

        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn('telegram_id');
        });
    }

    private function fillTelegramId(): void
    {
        \Illuminate\Support\Facades\DB::table('operators')
            ->leftJoin('clients', 'operators.client_id', '=', 'clients.id')
            ->update([
                'operators.telegram_id' => \Illuminate\Support\Facades\DB::raw('clients.telegram_id')
            ]);
    }
}
