<?php
namespace SED\Documents\Review\Seeders\Initial;

use SED\Documents\Review\Enums\ParticipantType;
use SED\Documents\Review\Models\ParticipantTypeModel;
use SED\Documents\Review\Seeders\SeederInterface;

class ParticipantTypeSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => ParticipantType::RESPONSIBLE,
				'title' => 'Инициатор',
			],
			[
				'id' => ParticipantType::RECEIVERS,
				'title' => 'Получатель',
			],

		];

		ParticipantTypeModel::query()->upsert($data, ['id'], ['title']);
	}
}
