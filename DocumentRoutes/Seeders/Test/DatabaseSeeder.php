<?php
namespace SED\DocumentRoutes\Seeders\Test;

class DatabaseSeeder extends \SED\DocumentRoutes\Seeders\DatabaseSeeder
{
	protected $classes = [
		PartitionTestSeeder::class,
		RouteTestSeeder::class,
		TemplateDocumentTestSeeder::class,
	];
}