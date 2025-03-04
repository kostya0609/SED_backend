<?php
namespace SED\DocumentRoutes\Seeders\Test;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Seeders\SeederInterface;
use SED\DocumentRoutes\Features\Routes\Dto\CreateRouteDto;
use SED\DocumentRoutes\Features\Routes\Services\RouteService;

class RouteTestSeeder implements SeederInterface
{
	private RouteService $service;

	public function __construct(RouteService $service)
	{
		$this->service = $service;
	}

	public function run()
	{
		$faker = \Faker\Factory::create();

		foreach (range(1, 200) as $_) {
			$route = new CreateRouteDto();
			$route->title = "Маршрут №$_";
			$route->direction_id = random_int(1, 6);
			$route->last_editor_id = $this->getUsers()->random();
			$route->description = $faker->paragraph();
			$route->partition_id = random_int(1, 22);
			$route->group_id = random_int(1, 4);
			$route->departments = [1074];
			$route->is_active = $faker->boolean();
			$route->user_id = $this->getUsers()->random();

			$this->service->create($route);
		}
	}

	private function getUsers(): Collection
	{
		return collect([
			14956,
			12467,
			14317,
			14287
		]);
	}
}