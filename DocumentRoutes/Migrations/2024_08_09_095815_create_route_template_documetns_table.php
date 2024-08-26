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
		Schema::create('l_route_tmp_docs', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('parent_id')->nullable();
			$table->unsignedBigInteger('route_id');
			$table->unsignedBigInteger('type_id');
			$table->string('title');
			$table->unsignedBigInteger('creator_id');
			$table->unsignedBigInteger('last_editor_id');
			$table->json('data');
			$table->text('requirements')->nullable();
			$table->boolean('is_active')->default(true);
			$table->boolean('is_start')->default(false);
			$table->timestamps();

			$table->foreign('route_id')->references('id')->on('l_route_routes')->cascadeOnDelete();

			$table->foreign('parent_id')->references('id')->on('l_route_tmp_docs')->cascadeOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_route_tmp_docs');
	}
};