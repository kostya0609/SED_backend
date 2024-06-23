<?php
namespace SED\Documents\Review\Seeders\Initial;

class DatabaseSeeder extends \SED\Documents\Review\Seeders\DatabaseSeeder
{
	protected $classes = [
		ReviewStatusSeeder::class,
        ParticipantTypeSeeder::class,
	];
}
