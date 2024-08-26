<?php

namespace SED\DocumentRoutes\Commands;

use Illuminate\Console\Command;
use SED\DocumentRoutes\Seeders\Initial\DatabaseSeeder;

class DocumentRoutesRebuild extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-document-routes:rebuild';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Перезапускает миграции и заново заполняет начальными и тестовыми данными. Используется только для разработки.';

	public function handle(DatabaseSeeder $seeder)
	{
		$this->info('Перезапуск миграций');
		$this->call('sed-document-routes:migrate', ['--reset' => true]);

		$this->info('Заполнение таблиц начальными данными');
		$this->call('sed-document-routes:seeding-initial');

		$this->info('Заполнение таблиц тестовыми данными');
		$this->call('sed-document-routes:seeding-test');
	}
}
