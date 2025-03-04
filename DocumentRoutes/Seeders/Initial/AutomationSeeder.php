<?php
namespace SED\DocumentRoutes\Seeders\Initial;

use SED\DocumentRoutes\AutomationSetting;
use SED\DocumentRoutes\Seeders\SeederInterface;
use SED\DocumentRoutes\Features\Automation\Models\Setting;

class AutomationSeeder implements SeederInterface
{
	function run()
	{
		$data = [
			[
				'id' => AutomationSetting::AUTORUN,
				'title' => 'Автозапуск',
				'description' => 'Автоматическое создание документов на базе дочерних шаблонов с автозапуском, когда текущий документ дойшел до финального статуса.',
				'default_is_active' => false,
				'default_data' => null,
			],
		];

		Setting::upsert($data, 'id', ['title', 'description', 'default_is_active', 'default_data']);
	}
}