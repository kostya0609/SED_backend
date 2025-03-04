<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	public function up()
	{
		Schema::table('l_directive', function (Blueprint $table) {
			$table->dateTime('execution_control_date')->nullable();
		});
	}

	public function down()
	{
		Schema::table('l_directive', function (Blueprint $table) {
			$table->dropColumn('execution_control_date');
		});
	}
};