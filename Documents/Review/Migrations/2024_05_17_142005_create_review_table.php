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
		Schema::create('l_review', function (Blueprint $table) {
			$table->id();
			$table->string('number')->nullable();
			$table->unsignedBigInteger('status_id');
			$table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('theme_id');
            $table->unsignedBigInteger('process_template_id');
			$table->unsignedBigInteger('department_id');

			$table->unsignedBigInteger('type_id')->default(\SED\Documents\Common\Enums\DocumentType::REVIEW);
			$table->timestamps();

			$table
				->foreign('type_id')
				->references('id')
				->on('l_sed_document_types');

			$table
				->foreign('status_id')
				->references('id')
				->on('l_review_statuses');

            $table
                ->foreign('theme_id')
                ->references('id')
                ->on('l_sed_document_themes')
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
		Schema::dropIfExists('l_review');
	}
};
