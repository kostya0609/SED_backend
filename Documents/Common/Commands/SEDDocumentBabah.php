<?php
namespace SED\Documents\Common\Commands;

use Illuminate\Console\Command;

class SEDDocumentBabah extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-documents:babah';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Передигивает миграции для всех модулей документов и заполняет их данными';

	public function handle()
	{
		$this->info('Удаление таблиц модуля Поручение');
		$this->call('sed-directive:migrate', ['--remove' => true]);

		$this->info('Удаление таблиц модуля Ознакомление');
		$this->call('sed-review:migrate', ['--remove' => true]);

		$this->info('Перезапуск общих миграций');
		$this->call('sed-documents:rebuild');

		$this->info('Перезапуск миграций модуля Поручение');
		$this->call('sed-directive:rebuild');

		$this->info('Перезапуск миграций модуля Ознакомление');
		$this->call('sed-review:rebuild');
	}
}