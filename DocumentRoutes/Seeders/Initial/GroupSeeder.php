<?php
namespace SED\DocumentRoutes\Seeders\Initial;

use SED\DocumentRoutes\Features\Routes\Models\Group;
use SED\DocumentRoutes\Seeders\SeederInterface;

/**
 * @deprecated Группы больше не используются
 */
class GroupSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'title' => 'Кадровые',
			],
			[
				'title' => 'Организационные',
			],
			[
				'title' => 'Финансово-учетные',
			],
			[
				'title' => 'Коммерческие',
			],
		];

		Group::query()->upsert($data, ['title']);
	}
}
