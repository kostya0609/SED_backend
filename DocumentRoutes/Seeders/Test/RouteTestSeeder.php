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

		foreach (range(1, 10) as $_) {
			$route = new CreateRouteDto();
			$route->title = $faker->title();
			$route->direction_id = 1;
			$route->creator_id = $this->getUsers()->random();
			$route->last_editor_id = $this->getUsers()->random();
			$route->description = $faker->paragraph();
			$route->partition_id = 1;
			$route->group_id = 1;
			$route->departments = [1074];
			$route->is_active = $faker->boolean();
			$route->user_id = 14956;

			$this->service->create($route);
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