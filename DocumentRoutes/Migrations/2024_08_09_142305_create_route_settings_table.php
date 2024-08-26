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
		Schema::create('l_route_settings', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->text('description')->nullable();
			$table->boolean('default_is_active')->default(false);
			$table->json('default_data');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_route_settings');
	}
};