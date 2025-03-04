<?php
namespace SED\Report;

use SED\Common\Services\BaseServiceProvider;
use SED\Report\Commands\SEDReportMigrate;

class ReportServiceProvider extends BaseServiceProvider
{
	protected array $commands = [
		SEDReportMigrate::class,
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
	}
}
