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
		Schema::create('l_route_availability', function (Blueprint $table) {

			$table->unsignedBigInteger('route_id');
			$table->unsignedBigInteger('department_id');

			$table->primary(['route_id', 'department_id']);

			$table->foreign('route_id')->references('id')->on('l_route_routes')->cascadeOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_route_availability');
	}
};