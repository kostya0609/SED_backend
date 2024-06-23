<?php
namespace SED\Documents\Common\Seeders\Test;

use SED\Documents\Common\Seeders\SeederInterface;

class DocumentThemeSeeder implements SeederInterface
{

	public function run()
	{
		$data = [
			[
				'id' => 1,
				'title' => 'Тестовая тема',
			],
			[
				'id' => 2,
				'title' => 'Тестовая тема 2',
			],
			[
				'id' => 3,
				'title' => 'Тестовая тема 3',
			],
		];

		\SED\Documents\Common\Models\DocumentTheme::query()->upsert($data, ['id'], ['title']);
	}
}