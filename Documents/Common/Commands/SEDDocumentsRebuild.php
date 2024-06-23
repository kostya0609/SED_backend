<?php
namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;

class SEDDocumentsRebuild extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-documents:rebuild';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Перезапускает миграции и заново заполняет начальными и тестовыми данными. Используется только для разработки.';

	public function handle()
	{
		$this->info('Перезапуск миграций');
		$this->call('sed-documents:migrate', ['--reset' => true]);

		$this->info('Заполнение таблиц начальными данными');
		$this->call('sed-documents:seeding-initial');

		$this->info('Заполнение таблиц тестовыми данными');
		$this->call('sed-documents:seeding-test');
	}
}
