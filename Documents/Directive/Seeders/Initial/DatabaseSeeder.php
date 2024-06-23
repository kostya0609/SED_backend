<?php
namespace SED\Documents\Directive\Seeders\Initial;

class DatabaseSeeder extends \SED\Documents\Directive\Seeders\DatabaseSeeder
{
	protected $classes = [
		DirectiveStatusSeeder::class,
		ParticipantTypeSeeder::class,
	];
}