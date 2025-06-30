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
		Schema::create('l_route_template_partitions', function (Blueprint $table) {
			$table->id();
			$table->string('title');
			$table->bigInteger('route_id');
			$table->bigInteger('creator_id');
			$table->bigInteger('last_editor_id');
			$table->bigInteger('parent_template_id');
			$table->boolean('is_active')->default(true);
            $table->timestamps();

        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('l_route_template_partitions');
	}
};
