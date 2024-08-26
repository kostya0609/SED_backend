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
		Schema::create('l_sed_document_hierarchy', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('document_id');
			$table->unsignedBigInteger('parent_document_id');
			$table->boolean('is_start')->default(false);
			$table->unsignedBigInteger('concrete_document_id');
			$table->string('number');

			$table
				->foreign('document_id')
				->references('id')
				->on('l_sed_documents')
				->cascadeOnDelete();

			$table
				->foreign('parent_document_id')
				->references('id')
				->on('l_sed_documents')
				->cascadeOnDelete();

			$table
				->foreign('concrete_document_id')
				->references('document_id')
				->on('l_sed_documents')
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
		Schema::dropIfExists('l_sed_document_hierarchy');
	}
};