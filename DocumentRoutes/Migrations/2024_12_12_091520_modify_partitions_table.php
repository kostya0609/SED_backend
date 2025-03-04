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
		Schema::table('l_route_partitions', function (Blueprint $table) {
			$table->unsignedBigInteger('creator_id');
			$table->unsignedBigInteger('last_editor_id');
			$table->boolean('is_active')->default(true);
			$table->timestamps();

		});
	}

	/**z
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('l_route_partitions', function (Blueprint $table) {
			$table->dropColumn('creator_id');
			$table->dropColumn('last_editor_id');
			$table->dropColumn('is_active');
			$table->dropTimestamps();
		});
	}
};