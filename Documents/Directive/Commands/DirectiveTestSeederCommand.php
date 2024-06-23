<?php
namespace SED\Documents\Directive\Commands;

use Illuminate\Console\Command;
use SED\Documents\Directive\Seeders\Test\DatabaseSeeder;

class DirectiveTestSeederCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-directive:seeding-test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Заполнение тестовыми данными модуля';

	public function handle(DatabaseSeeder $seeder)
	{
		$this->info('Заполнение таблиц модуля тестовыми данными');
		$seeder->run();
	}
}
