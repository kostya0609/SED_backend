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
		Schema::create('l_esz_content', function (Blueprint $table) {
			$table->unsignedBigInteger('esz_id');
			$table->text('content');
			$table->text('portfolio')->nullable();

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
		Schema::dropIfExists('l_esz_content');
	}
};