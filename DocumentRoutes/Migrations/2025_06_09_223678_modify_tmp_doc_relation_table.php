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
            $table->dropUnique('l_route_tmp_doc_relations_parent_child_root_pk');
            $table->unique([
                'parent_template_id',
                'child_template_id',
                'root_template_id',
                'parent_template_type',
                'child_template_type'
            ], 'l_route_tmp_doc_relations_parent_child_root_types_pk');

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

            $table->unique(['parent_template_id', 'child_template_id', 'root_template_id'], 'l_route_tmp_doc_relations_parent_child_root_pk');
            $table->dropUnique('l_route_tmp_doc_relations_parent_child_root_types_pk');

        });
	}
};
