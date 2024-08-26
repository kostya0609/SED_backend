<?php
namespace SED;

use SED\Report\ReportServiceProvider;
use Illuminate\Support\ServiceProvider;
use SED\DocumentRoutes\DocumentRoutesServiceProvider;
use SED\Documents\DocumentsServiceProvider;

class SEDServiceProvider extends ServiceProvider
{
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
        $this->loadRoutesFrom(__DIR__ . '/Common/routes.php');

        $this->app->register(DocumentsServiceProvider::class);
        $this->app->register(ReportServiceProvider::class);
        $this->app->register(DocumentRoutesServiceProvider::class);
    }
}
