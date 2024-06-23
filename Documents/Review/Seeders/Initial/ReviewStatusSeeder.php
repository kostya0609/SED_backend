<?php
namespace SED\Documents\Review\Seeders\Initial;

use SED\Documents\Review\Enums\Status;
use SED\Documents\Review\Models\StatusModel;
use SED\Documents\Review\Seeders\SeederInterface;

class ReviewStatusSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => Status::PREPARATION,
				'title' => 'Подготовка',
			],
			[
				'id' => Status::REVIEW,
				'title' => 'Ознакомление',
			],
			[
				'id' => Status::ARCHIVE_WORKED,
				'title' => 'Архив. Отработано',
			],
            [
                'id' => Status::ARCHIVE_CANCELLED,
                'title' => 'Архив. Аннулировано',
            ],
		];

		StatusModel::query()->upsert($data, ['id'], ['title']);
	}
}
