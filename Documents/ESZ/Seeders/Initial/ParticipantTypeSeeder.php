<?php
namespace SED\Documents\ESZ\Seeders\Initial;

use SED\Documents\ESZ\Enums\ParticipantType;
use SED\Documents\ESZ\Models\ParticipantTypeModel;
use SED\Documents\ESZ\Seeders\SeederInterface;

class ParticipantTypeSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => ParticipantType::INITIATOR,
				'title' => 'Инициатор',
			],
			[
				'id' => ParticipantType::SIGNATORY,
				'title' => 'Подписант',
			],
			[
				'id' => ParticipantType::RECEIVERS,
				'title' => 'Адресат',
			],
			[
				'id' => ParticipantType::OBSERVERS,
				'title' => 'Наблюдатель',
			],

		];

		ParticipantTypeModel::query()->upsert($data, ['id'], ['title']);
	}
}
