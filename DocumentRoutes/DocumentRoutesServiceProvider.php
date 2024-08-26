<?php
namespace SED\DocumentRoutes;

use SED\DocumentRoutes\Commands\{
	DocumentRoutesMigrate,
	DocumentRoutesSeeder,
	DocumentRoutesTestSeeder,
	DocumentRoutesRebuild
};
use SED\Common\Services\BaseServiceProvider;

class DocumentRoutesServiceProvider extends BaseServiceProvider
{

	protected array $commands = [
		DocumentRoutesMigrate::class,
		DocumentRoutesSeeder::class,
		DocumentRoutesTestSeeder::class,
		DocumentRoutesRebuild::class,
	];

	public function register(): void
	{

	}

	public function boot(): void
	{
		$this->loadRoutesFrom(__DIR__ . '/routes.php');
		$this->commands($this->commands);
	}
}