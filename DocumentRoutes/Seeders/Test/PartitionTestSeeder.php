<?php
namespace SED\DocumentRoutes\Seeders\Test;

use SED\DocumentRoutes\Features\Partitions\Dto\CreatePartitionDto;
use SED\DocumentRoutes\Features\Partitions\Services\PartitionService;
use SED\DocumentRoutes\Seeders\SeederInterface;

class PartitionTestSeeder implements SeederInterface
{
	private PartitionService $service;
	private array $partitions = [
		['title' => 'Кадрово-административные', 'parent_id' => null],
		['title' => 'Согласование кандидата и организация трудоустройства', 'parent_id' => 1],
		['title' => 'Подача заявки на подбор персонала', 'parent_id' => 1],
		['title' => 'Изменение квалификационной категории сотрудника', 'parent_id' => 1],
		['title' => 'Отпуск', 'parent_id' => 1],

		['title' => 'Договоры', 'parent_id' => null],
		['title' => 'Договор оферты', 'parent_id' => 6],
        ['title' => 'Договор организации', 'parent_id' => 6],
        ['title' => 'Договор поставки', 'parent_id' => 6],
        ['title' => 'Договор пуска', 'parent_id' => 6],
		['title' => 'Договор согласия на предоставление услуг', 'parent_id' => 8],
		['title' => 'Договор на услуги по продаже и продаже автомобилей', 'parent_id' => 8],
        ['title' => 'Договор на услуги по продаже и продаже строительных материалов', 'parent_id' => 8],
		['title' => 'Договор на техническое обслуживание и ремонт техники приобретение ГСМ материалов запчастей техники', 'parent_id' => 10],

		['title' => 'НРТ', 'parent_id' => null],
        ['title' => 'Коммерческие', 'parent_id' => 15],
        ['title' => 'Стажировка в военной кафедре', 'parent_id' => 15],
		['title' => 'Тренировочный период', 'parent_id' => 17],
        ['title' => 'Тренировочный период в военной кафедре', 'parent_id' => 18],
		['title' => 'Стажировка в военной кафедре по обучению военнослужащих', 'parent_id' => 19],
        ['title' => 'Стажировка в военной кафедре по обучению военнослужащих и военной охраны', 'parent_id' => 20],
		['title' => 'Стажировка в военной кафедре по обучению военнослужащих и военной охраны и обучению военнослужащих', 'parent_id' => 20],
	];

	public function __construct(PartitionService $service)
	{
		$this->service = $service;
	}

	public function run()
	{
		$faker = \Faker\Factory::create();

		foreach ($this->partitions as $part) {
			$dto = new CreatePartitionDto();
			$dto->title = $part['title'];
			$dto->parent_id = $part['parent_id'];

			$this->service->create($dto);
		}
	}
}