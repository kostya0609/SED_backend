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
		Schema::create('l_route_tmp_doc_relations', function (Blueprint $table) {
			$table->unsignedBigInteger('id');
			$table->unsignedBigInteger('parent_template_id');
			$table->unsignedBigInteger('child_template_id');
			$table->unsignedBigInteger('root_template_id')->nullable();

			$table->unique(['parent_template_id', 'child_template_id', 'root_template_id'], 'l_route_tmp_doc_relations_parent_child_root_pk');

			$table
				->foreign('parent_template_id')
				->references('id')
				->on('l_route_tmp_docs')
				->onDelete('cascade');

			$table
				->foreign('child_template_id')
				->references('id')
				->on('l_route_tmp_docs')
				->onDelete('cascade');

			$table
				->foreign('root_template_id')
				->references('id')
				->on('l_route_tmp_docs')
				->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_route_tmp_doc_relations');
	}
};