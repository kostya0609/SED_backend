<?php
namespace SED\Documents\Review;

use SED\Common\Services\BaseServiceProvider;
use SED\Documents\Review\Commands\{
	ReviewMigrate,
	ReviewRebuildCommand,
	ReviewTestSeederCommand,
	ReviewInitialSeederCommand
};

class ReviewServiceProvider extends BaseServiceProvider
{
	protected array $providers = [
		ReviewEventServiceProvider::class,
	];

	protected array $commands = [
		ReviewMigrate::class,
		ReviewInitialSeederCommand::class,
		ReviewTestSeederCommand::class,
		ReviewRebuildCommand::class,
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
