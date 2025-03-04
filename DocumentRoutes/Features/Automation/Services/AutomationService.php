<?php
namespace SED\DocumentRoutes\Features\Automation\Services;

use App\Modules\BsiTable\FilterFacade;
use SED\DocumentRoutes\Features\Automation\Models\Setting;
use SED\DocumentRoutes\Features\Automation\Models\SettingValue;

class AutomationService
{
	public function getAll(int $tmp_doc_id)
	{
		$query = Setting::query()
			->select([
				'l_route_settings.id',
				'l_route_settings.title',
				'l_route_settings.description',
				'l_route_setting_values.tmp_doc_id',
				\DB::raw('(if (l_route_setting_values.is_active is null, l_route_settings.default_is_active, l_route_setting_values.is_active)) as is_active'),
			])
			->leftJoin('l_route_setting_values', function ($query) use ($tmp_doc_id) {
				$query
					->on('l_route_setting_values.setting_id', '=', 'l_route_settings.id')
					->where('l_route_setting_values.tmp_doc_id', $tmp_doc_id);
			});

		$search_fields = [
			'id' => '%like%',
			'title' => '%like%',
			'description' => '%like%',
		];

		return FilterFacade::sort()
			->filter()
			->search($search_fields)
			->getAll($query);
	}

	public function updateIsActive(int $id, int $tmp_doc_id, bool $is_active)
	{
		SettingValue::upsert(
			[
				'setting_id' => $id,
				'tmp_doc_id' => $tmp_doc_id,
				'is_active' => $is_active,
			],
			[
				'setting_id',
				'tmp_doc_id',
			],
			[
				'is_active',
			]
		);
	}

	public function getSetting(int $tmp_doc_id, int $setting_id)
	{
		$setting = Setting::query()
			->select([
				'l_route_settings.id',
				'l_route_settings.title',
				'l_route_settings.description',
				'l_route_setting_values.tmp_doc_id',
				\DB::raw('(if (l_route_setting_values.is_active is null, l_route_settings.default_is_active, l_route_setting_values.is_active)) as is_active'),
			])
			->leftJoin('l_route_setting_values', function ($query) use ($tmp_doc_id) {
				$query
					->on('l_route_setting_values.setting_id', '=', 'l_route_settings.id')
					->where('l_route_setting_values.tmp_doc_id', $tmp_doc_id);
			})
			->where('id', $setting_id)
			->first();

		if (!$setting) {
			throw new \DomainException("Не удалось найти настройку автоматизации по id $setting_id и id шаблона документа $tmp_doc_id");
		}

		return $setting;
	}
}