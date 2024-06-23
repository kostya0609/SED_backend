<?php
namespace SED\Documents\Directive\Seeders\Initial;

use SED\Documents\Directive\Enums\ParticipantType;
use SED\Documents\Directive\Models\ParticipantTypeModel;
use SED\Documents\Directive\Seeders\SeederInterface;

class ParticipantTypeSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => ParticipantType::CREATOR,
				'title' => 'Создатель',
			],
			[
				'id' => ParticipantType::AUTHOR,
				'title' => 'Автор',
			],
			[
				'id' => ParticipantType::EXECUTORS,
				'title' => 'Исполнители',
			],
			[
				'id' => ParticipantType::CONTROLLERS,
				'title' => 'Контроллеры',
			],
			[
				'id' => ParticipantType::OBSERVERS,
				'title' => 'Наблюдатели',
			],
		];

		ParticipantTypeModel::query()->upsert($data, ['id'], ['title']);
	}
}