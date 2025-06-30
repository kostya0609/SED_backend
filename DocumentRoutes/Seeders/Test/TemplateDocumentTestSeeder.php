<?php
namespace SED\DocumentRoutes\Seeders\Test;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Seeders\SeederInterface;
use SED\DocumentRoutes\Features\DocumentTemplates\Services\DocumentTemplateService;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Models\User;

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

		foreach (range(1, 200) as $_) {
			$dto = new \SED\DocumentRoutes\Features\DocumentTemplates\Dto\CreateDocTmpDto();
			$dto->title = "Шаблон документа №$_";

			$dto->route_id = random_int(1, 50);

			$dto->parents = [];

			$dto->type_id = collect([DocumentType::ESZ, DocumentType::DIRECTIVE, DocumentType::REVIEW])->random();
			$dto->creator_id = $this->getUsers()->random();
			$dto->last_editor_id = $this->getUsers()->random();
			$dto->is_start = false;
			$dto->is_active = true;
			$dto->requirements = $faker->paragraph(3);
			$dto->user_id = 14956;

			$dto->data = $this->createDocumentData($dto->type_id);

			$this->service->create($dto);
		}
	}

	private function createDocumentData(int $type_id): array
	{
		switch ($type_id) {
			case DocumentType::ESZ:
				return $this->createEszTemplate();
			case DocumentType::DIRECTIVE:
				return $this->createDirectiveTemplate();
			case DocumentType::REVIEW:
				return $this->createReviewTemplate();
			default:
				return [];
		}
	}

	private function createEszTemplate(): array
	{
		$faker = \Faker\Factory::create();

		return [
			'content' => 'Текст шаблона ЭСЗ. ' . $faker->paragraph(),
			'portfolio' => 'Описание портфолио шаблона ЭСЗ. ' . $faker->paragraph(),
			'signatory' => User::find($this->getUsers()->random()),
			'receivers' => $this->getUsers()->random(random_int(1, 5))->map(fn($user_id) => User::find($user_id))->values()->toArray(),
			'observers' => $this->getUsers()->random(random_int(1, 5))->map(fn($user_id) => User::find($user_id))->values()->toArray(),
		];
	}

	private function createDirectiveTemplate(): array
	{
		$faker = \Faker\Factory::create();

		return [
			'content' => 'Текст шаблона поручения. ' . $faker->paragraph(),
			'portfolio' => 'Описание портфолио шаблона поручения. ' . $faker->paragraph(),
			'days_amount' => random_int(1, 100),
			'author' => User::find($this->getUsers()->random()),
			'executors' => $this->getUsers()->random(random_int(1, 5))->map(fn($user_id) => User::find($user_id))->values()->toArray(),
			'controllers' => $this->getUsers()->random(random_int(1, 5))->map(fn($user_id) => User::find($user_id))->values()->toArray(),
			'observers' => $this->getUsers()->random(random_int(1, 5))->map(fn($user_id) => User::find($user_id))->values()->toArray(),
		];
	}

	private function createReviewTemplate(): array
	{
		$faker = \Faker\Factory::create();

		return [
			'content' => 'Текст шаблона ознаколмения. ' . $faker->paragraph(),
			'portfolio' => 'Описание портфолио шаблона ознаколмения. ' . $faker->paragraph(),
			'receivers' => $this->getUsers()->random(random_int(1, 5))->map(fn($user_id) => User::find($user_id))->values()->toArray(),
		];
	}


	private function getUsers(): Collection
	{
		return collect([
			3549,
			5007,
			5388,
			5390,
			5392,
			5393,
			5398,
			5400,
			5403,
			5405,
			5406,
			5408,
			5412,
			5413,
			5414,
			5415,
			5421,
			5423,
			5431,
			5435,
			5436,
			5437,
			5439,
			5440,
			5442,
			5443,
			5447,
			5448,
			5450,
			5452,
			5453,
			5457,
			5458,
			5459,
			5460,
		]);
	}
}