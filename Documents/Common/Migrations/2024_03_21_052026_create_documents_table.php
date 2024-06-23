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
		Schema::create('l_sed_documents', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('document_id');
			$table->string('number');
			$table->unsignedBigInteger('type_id');
			$table->string('theme');
			$table->unsignedBigInteger('executor_id');
			$table->string('status_title');
			$table->timestamps();

			$table->unique(['document_id', 'type_id']);

			$table
				->foreign('type_id')
				->references('id')
				->on('l_sed_document_types')
				->restrictOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_sed_documents');
	}
};