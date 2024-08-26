<?php
namespace SED\DocumentRoutes\Seeders\Test;

use SED\DocumentRoutes\Features\Partitions\Dto\CreatePartitionDto;
use SED\DocumentRoutes\Features\Partitions\Services\PartitionService;
use SED\DocumentRoutes\Seeders\SeederInterface;

class PartitionTestSeeder implements SeederInterface
{
	private PartitionService $service;

	public function __construct(PartitionService $service)
	{
		$this->service = $service;
	}

	public function run()
	{
		$faker = \Faker\Factory::create();

		foreach (range(1, 10) as $_) {
			$dto = new CreatePartitionDto();
			$dto->title = $faker->title();
			$dto->parent_id = null;
			
			$this->service->create($dto);
		}
	}
}