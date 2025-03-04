<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::statement("
           CREATE VIEW l_partition_route AS
            SELECT 
               NULL AS route_id,
               partitions.id AS partition_id,
               partitions.title AS title,
               partitions.parent_id AS parent_id,
               partitions.is_active AS is_active,
               partitions.creator_id AS creator_id,
               partitions.last_editor_id AS last_editor_id,
               partitions.created_at AS created_at,
               partitions.updated_at AS updated_at,
               'partition' AS type
            FROM l_route_partitions AS partitions
            UNION ALL
            SELECT  
               routes.id AS route_id,
               NULL AS partition_id,
               routes.title AS title,
               routes.partition_id AS parent_id,
               routes.is_active AS is_active,
               routes.creator_id AS creator_id,
               routes.last_editor_id AS last_editor_id,
               routes.created_at AS created_at,
               routes.updated_at AS updated_at,          
               'route' AS type
           FROM
               l_route_routes AS routes   
       ");
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement("DROP VIEW IF EXISTS l_partition_route");
	}
};