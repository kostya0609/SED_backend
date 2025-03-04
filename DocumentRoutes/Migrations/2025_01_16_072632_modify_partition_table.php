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
		Schema::table('l_route_partitions', function (Blueprint $table) {
			$table->dropForeign(['parent_id']);
			$table
				->foreign('parent_id')
				->references('id')
				->on('l_route_partitions')
				->restrictOnDelete();
		});
	}

	/**z
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('l_route_partitions', function (Blueprint $table) {
			$table->dropForeign(['parent_id']);
			$table
				->foreign('parent_id')
				->references('id')
				->on('l_route_partitions')
				->cascadeOnDelete();
		});
	}
};