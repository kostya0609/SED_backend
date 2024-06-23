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
		Schema::create('l_review_content', function (Blueprint $table) {
            $table->unsignedBigInteger('review_id');
			$table->text('content');
			$table->text('portfolio')->nullable();

            $table
                ->foreign('review_id')
                ->references('id')
                ->on('l_review')
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
		Schema::dropIfExists('l_review_content');
	}
};
