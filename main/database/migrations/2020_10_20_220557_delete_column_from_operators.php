<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteColumnFromOperators extends Migration
{
    public function up(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn('telegram_name');
            $table->dropColumn('telegram_id');
        });
    }

    public function down(): void
    {
    }
}
