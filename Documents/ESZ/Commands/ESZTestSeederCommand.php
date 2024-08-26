<?php
namespace SED\Documents\ESZ\Commands;

use Illuminate\Console\Command;
use SED\Documents\ESZ\Seeders\Test\DatabaseSeeder;

class ESZTestSeederCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-esz:seeding-test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Заполнение тестовыми данными модуля управления документами СЭД';

	public function handle(DatabaseSeeder $seeder)
	{
		$this->info('Заполнение таблиц модуля тестовыми данными');
		$seeder->run();
	}
}
