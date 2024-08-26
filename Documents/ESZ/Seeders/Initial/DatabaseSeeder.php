<?php
namespace SED\Documents\ESZ\Seeders\Initial;

class DatabaseSeeder extends \SED\Documents\ESZ\Seeders\DatabaseSeeder
{
	protected $classes = [
		ESZStatusSeeder::class,
        ParticipantTypeSeeder::class,
	];
}
