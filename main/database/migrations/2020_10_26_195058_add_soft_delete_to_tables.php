<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteToTables extends Migration
{
	public function up()
	{
		Schema::table('sellers', function (Blueprint $table) {
			$table->softDeletes();
		});

        Schema::table('orders', function (Blueprint $table) {
            $table->softDeletes();
        });
	}

	public function down()
	{
	}
}
