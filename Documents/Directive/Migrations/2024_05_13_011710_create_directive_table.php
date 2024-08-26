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
		Schema::create('l_directive', function (Blueprint $table) {
			$table->id();
			$table->string('number')->nullable();
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('process_template_id')->nullable();
			$table->unsignedBigInteger('department_id');
			$table->unsignedBigInteger('type_id')->default(\SED\Documents\Common\Enums\DocumentType::DIRECTIVE);
			$table->unsignedBigInteger('tmp_doc_id')->nullable();
			$table->unsignedBigInteger('common_document_id')->nullable();
			$table->string('theme_title')->nullable();
			$table->date('executed_at');
			$table->timestamps();

			$table
				->foreign('type_id')
				->references('id')
				->on('l_sed_document_types')
				->restrictOnDelete();

			$table
				->foreign('status_id')
				->references('id')
				->on('l_directive_statuses')
				->restrictOnDelete();

			$table
				->foreign('common_document_id')
				->references('id')
				->on('l_sed_documents')
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
		Schema::dropIfExists('l_directive');
	}
};