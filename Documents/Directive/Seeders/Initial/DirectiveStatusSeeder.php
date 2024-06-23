<?php
namespace SED\Documents\Directive\Seeders\Initial;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\StatusModel;
use SED\Documents\Directive\Seeders\SeederInterface;

class DirectiveStatusSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => Status::PREPARATION,
				'title' => 'Подготовка',
			],
			[
				'id' => Status::EXECUTION_CHANGE_REQUEST,
				'title' => 'Исполнение. Запрос изменения',
			],
			[
				'id' => Status::EXECUTION_IN_WORK,
				'title' => 'Исполнение. В работе',
			],
			[
				'id' => Status::EXECUTION_CONTROL,
				'title' => 'Контроль исполнения',
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