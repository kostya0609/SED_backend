<?php
namespace SED\DocumentRoutes\Seeders\Initial;

use SED\DocumentRoutes\Features\Routes\Models\Direction;
use SED\DocumentRoutes\Seeders\SeederInterface;

class DirectionSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'title' => 'НОТ',
			],
            [
				'title' => 'НР',
			],
            [
				'title' => 'НРТ',
			],
			[
				'title' => 'НТЛ',
			],
			[
                'title' => 'УН',
            ],
			[
				'title' => 'ЦО',
			],
		];

        Direction::query()->upsert($data, ['title']);
	}
}
