<?php
namespace SED\Documents;

use Illuminate\Support\ServiceProvider;
use SED\Documents\ESZ\ESZServiceProvider;
use SED\Documents\Review\ReviewServiceProvider;
use SED\Documents\Directive\DirectiveServiceProvider;
use SED\Documents\Common\Commands\{
	SEDDocumentsMigrate,
	SEDDocumentsSeeder,
	SEDDocumentsTestSeeder,
	SEDDocumentsRebuild,
	SEDDocumentBabah
};

class DocumentsServiceProvider extends ServiceProvider
{
	protected array $commands = [
		SEDDocumentsMigrate::class,
		SEDDocumentsSeeder::class,
		SEDDocumentsTestSeeder::class,
		SEDDocumentsRebuild::class,
		SEDDocumentBabah::class,
	];

	protected array $providers = [
		ESZServiceProvider::class,
		DirectiveServiceProvider::class,
		ReviewServiceProvider::class,
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
		$this->loadRoutesFrom(__DIR__ . '/Common/Routes/routes_v1.php');
		$this->commands($this->commands);

		foreach ($this->providers as $provider) {
			$this->app->register($provider);
		}
	}
}
