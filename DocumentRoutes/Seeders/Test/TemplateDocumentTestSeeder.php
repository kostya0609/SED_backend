<?php
namespace SED\DocumentRoutes\Seeders\Test;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Seeders\SeederInterface;
use SED\DocumentRoutes\Features\DocumentTemplates\Services\DocumentTemplateService;

class TemplateDocumentTestSeeder implements SeederInterface
{
	private DocumentTemplateService $service;

	public function __construct(DocumentTemplateService $service)
	{
		$this->service = $service;
	}

	public function run()
	{
		$faker = \Faker\Factory::create();

		foreach (range(0, 10) as $_) {
			$dto = new \SED\DocumentRoutes\Features\DocumentTemplates\Dto\CreateDocTmpDto();
			$dto->title = $faker->text();
			$dto->parent_id = null;
			$dto->route_id = 1;
			$dto->type_id = collect([1, 2, 3])->random();
			$dto->creator_id = $this->getUsers()->random();
			$dto->last_editor_id = $this->getUsers()->random();
			$dto->data = ['key' => 'value'];
			$dto->is_start = collect([true, false])->random();
			$dto->is_active = collect([true, false])->random();
			$dto->requirements = $faker->paragraph(3);
			$dto->user_id = 14956;

			$this->service->create($dto);
		}
	}

	private function getUsers(): Collection
	{
		return collect([

			13186,
			14165,
			6292,
			6261,
			7850,
			14653,
			7927,
			6142,
			15214,
			14307,
			14256,
			14754,
			14476,
			13548,
			13332,
			6072,
			14467,
			14805,
			13343,
			6115,
			6144,
			14601,
		]);
	}
}