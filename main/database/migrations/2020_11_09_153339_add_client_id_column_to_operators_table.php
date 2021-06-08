<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdColumnToOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasColumn('operators', 'client_id')) {
            return;
        }

        Schema::table('operators', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('number');

            $table->foreign('client_id')
                ->on('clients')
                ->references('id')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (!Schema::hasColumn('operators', 'client_id')) {
            return;
        }

        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
}
