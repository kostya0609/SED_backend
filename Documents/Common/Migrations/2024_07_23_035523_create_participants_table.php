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
		Schema::create('l_sed_document_participants', function (Blueprint $table) {
			$table->unsignedBigInteger('document_id');
			$table->unsignedBigInteger('user_id');

			$table
				->foreign('document_id')
				->references('id')
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
		Schema::dropIfExists('l_sed_document_participants');
	}
};