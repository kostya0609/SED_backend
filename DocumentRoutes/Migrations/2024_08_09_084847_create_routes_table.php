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
		Schema::create('l_route_routes', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->unsignedBigInteger('group_id');
			$table->unsignedBigInteger('direction_id');
			$table->unsignedBigInteger('creator_id');
			$table->unsignedBigInteger('last_editor_id');
			$table->unsignedBigInteger('partition_id');
			$table->text('description')->nullable();
			$table->boolean('is_active')->default(true);
			$table->timestamps();

			$table
				->foreign('direction_id')
				->references('id')
				->on('l_route_directions')
				->restrictOnDelete();

			$table
				->foreign('group_id')
				->references('id')
				->on('l_route_groups')
				->restrictOnDelete();

			$table
				->foreign('partition_id')
				->references('id')
				->on('l_route_partitions')
				->restrictOnDelete();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_route_routes');
	}
};