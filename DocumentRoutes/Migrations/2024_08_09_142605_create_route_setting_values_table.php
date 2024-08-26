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

		Schema::create('l_route_setting_values', function (Blueprint $table) {
			$table->unsignedBigInteger('setting_id');
			$table->unsignedBigInteger('tmp_doc_id');
			$table->boolean('is_active')->default(false);
			$table->json('data');

			$table->primary(['setting_id', 'tmp_doc_id']);

			$table->foreign('setting_id', 'l_route_set_val_set_id_foreign')->references('id')->on('l_route_settings')->restrictOnDelete();
			$table->foreign('setting_id')->references('id')->on('l_route_tmp_docs')->cascadeOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('l_route_setting_values', function (Blueprint $table) {
			$table->dropForeign('l_route_set_val_set_id_foreign');
		});


		Schema::dropIfExists('l_route_setting_values');
	}
};
