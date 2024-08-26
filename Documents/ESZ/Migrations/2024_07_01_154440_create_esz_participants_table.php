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
		Schema::create('l_esz_participants', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('esz_id');
			$table->unsignedBigInteger('type_id');
			$table->unsignedBigInteger('user_id');

			$table
				->foreign('type_id')
				->references('id')
				->on('l_esz_participant_types')
				->restrictOnDelete();

			$table
				->foreign('esz_id')
				->references('id')
				->on('l_esz')
				->cascadeOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_esz_participants');
	}
};