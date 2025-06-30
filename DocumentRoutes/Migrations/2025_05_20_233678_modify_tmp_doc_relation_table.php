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
		Schema::table('l_route_tmp_doc_relations', function (Blueprint $table) {
			$table->string('parent_template_type')->default('template');
			$table->string('child_template_type')->default('template');

//            $table->dropForeign(['parent_template_id', 'child_template_id', 'root_template_id']);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('l_route_tmp_doc_relations', function (Blueprint $table) {
			$table->dropColumn('parent_template_type');
			$table->dropColumn('child_template_type');
//            $table
//                ->foreign('parent_template_id')
//                ->references('id')
//                ->on('l_route_tmp_docs')
//                ->onDelete('cascade');
//
//            $table
//                ->foreign('child_template_id')
//                ->references('id')
//                ->on('l_route_tmp_docs')
//                ->onDelete('cascade');
//
//            $table
//                ->foreign('root_template_id')
//                ->references('id')
//                ->on('l_route_tmp_docs')
//                ->onDelete('cascade');
		});
	}
};
