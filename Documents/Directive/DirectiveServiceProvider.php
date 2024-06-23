<?php
namespace SED\Documents\Directive;

use SED\Common\Services\BaseServiceProvider;
use SED\Documents\Directive\Commands\{
	DirectiveInitialSeederCommand,
	DirectiveMigrate,
	DirectiveRebuildCommand,
	DirectiveTestSeederCommand
};

class DirectiveServiceProvider extends BaseServiceProvider
{
	protected array $providers = [
		DirectiveEventServiceProvider::class,
	];

	protected array $commands = [
		DirectiveMigrate::class,
		DirectiveInitialSeederCommand::class,
		DirectiveTestSeederCommand::class,
		DirectiveRebuildCommand::class,
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
