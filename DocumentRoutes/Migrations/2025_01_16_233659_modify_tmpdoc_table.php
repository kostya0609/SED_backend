<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyTmpdocTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('l_route_tmp_docs', function (Blueprint $table) {
			$table->dropForeign(['route_id']);
			$table->foreign('route_id')->references('id')->on('l_route_routes')->restrictOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('l_route_tmp_docs', function (Blueprint $table) {
			$table->dropForeign(['route_id']);
			$table->foreign('route_id')->references('id')->on('l_route_routes')->cascadeOnDelete();
		});
	}
}
