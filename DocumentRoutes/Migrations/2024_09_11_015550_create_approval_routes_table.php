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
		Schema::create('l_route_approval_routes', function (Blueprint $table) {
			$table->unsignedBigInteger('tmp_doc_id');
			$table->unsignedBigInteger('approval_route_id');

			$table->primary(['tmp_doc_id', 'approval_route_id']);

			$table
				->foreign('tmp_doc_id')
				->references('id')
				->on('l_route_tmp_docs')
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
		Schema::dropIfExists('l_route_approval_routes');
	}
};