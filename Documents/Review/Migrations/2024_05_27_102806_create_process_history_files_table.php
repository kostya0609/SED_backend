<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('l_review_process_history_files', function (Blueprint $table) {
			$table->unsignedBigInteger('history_id');
			$table->unsignedBigInteger('file_id');

			$table
				->foreign('history_id')
				->references('id')
				->on('l_review_process_history')
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
		Schema::dropIfExists('l_review_process_history_files');
	}
};