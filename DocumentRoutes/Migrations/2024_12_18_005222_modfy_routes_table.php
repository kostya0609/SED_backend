<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('l_route_routes', function (Blueprint $table) {
			$table->unsignedBigInteger('direction_id')->nullable()->change();
			$table->unsignedBigInteger('group_id')->nullable()->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('l_route_routes', function (Blueprint $table) {
			$table->unsignedBigInteger('direction_id')->nullable(false)->change();
			$table->unsignedBigInteger('group_id')->nullable(false)->change();
		});
	}
};