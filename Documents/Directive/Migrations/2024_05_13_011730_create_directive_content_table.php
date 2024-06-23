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
		Schema::create('l_directive_content', function (Blueprint $table) {
			$table->unsignedBigInteger('directive_id');
			$table->text('content');
			$table->text('portfolio')->nullable();

			$table
				->foreign('directive_id')
				->references('id')
				->on('l_directive')
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
		Schema::dropIfExists('l_directive_content');
	}
};