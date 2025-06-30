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
			$table->unsignedBigInteger('parent_document_id')->nullable();
			$table->boolean('is_start')->default(false);
			$table->unsignedBigInteger('concrete_document_id');
			$table->unsignedBigInteger('start_document_id');
			$table->string('number');
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