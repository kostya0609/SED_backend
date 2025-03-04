<?php
namespace SED\Documents\ESZ\Seeders\Initial;

use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Models\StatusModel;
use SED\Documents\ESZ\Seeders\SeederInterface;

class ESZStatusSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => Status::PREPARATION,
				'title' => 'Подготовка',
			],

			[
				'id' => Status::FIX,
				'title' => 'Устранение замечаний',
			],

			[
				'id' => Status::COORDINATION,
				'title' => 'Согласование',
			],


			[
				'id' => Status::FIX_SIGNING,
				'title' => 'Устранение замечаний на подписании',
			],


			[
				'id' => Status::SIGNING,
				'title' => 'Подписание',
			],

			[
				'id' => Status::FIX_RESOLUTION,
				'title' => 'Устранение замечаний на резолюции',
			],

			[
				'id' => Status::RESOLUTION,
				'title' => 'Наложение резолюции',
			],

			[
				'id' => Status::ARCHIVE_WORKED,
				'title' => 'Архив. Отработано',
			]
			,
			[
				'id' => Status::ARCHIVE_CANCELLED,
				'title' => 'Архив. Аннулировано',
			],
			[
				'id' => Status::DRAFT,
				'title' => 'Черновик',
			],
		];

		StatusModel::query()->upsert($data, ['id'], ['title']);
	}
}
