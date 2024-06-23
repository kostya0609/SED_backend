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
		Schema::create('l_review_participants', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('review_id');
			$table->unsignedBigInteger('type_id');
			$table->unsignedBigInteger('user_id');

			$table
				->foreign('type_id')
				->references('id')
				->on('l_review_participant_types')
                ->restrictOnDelete();

			$table
				->foreign('review_id')
				->references('id')
				->on('l_review')
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
		Schema::dropIfExists('l_review_participants');
	}
};
