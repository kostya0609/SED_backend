<?php
namespace SED\Documents\ESZ;

use SED\Common\Services\BaseServiceProvider;
use SED\Documents\ESZ\Commands\{
	ESZInitialSeederCommand,
	ESZMigrate,
	ESZRebuildCommand,
	ESZTestSeederCommand,
	ESZForceDeleteCommand
};

class ESZServiceProvider extends BaseServiceProvider
{
	protected array $providers = [
		ESZEventServiceProvider::class,
	];

	protected array $commands = [
		ESZMigrate::class,
		ESZInitialSeederCommand::class,
		ESZTestSeederCommand::class,
		ESZRebuildCommand::class,
		ESZForceDeleteCommand::class,
	];

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadRoutesFrom(__DIR__ . '/Routes/routes_v1.php');
		$this->commands($this->commands);

		foreach ($this->providers as $provider) {
			$this->app->register($provider);
		}

		$this->loadEnvironmentsFrom(__DIR__);
	}
}
