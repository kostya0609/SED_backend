<?php
namespace SED\Documents\Directive\Commands;

use Illuminate\Console\Command;
use SED\Documents\Directive\Services\DirectiveService;

class DirectiveForceDeleteCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-directive:force-delete {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Принудительное удаление Поручения';

	public function handle(DirectiveService $service)
	{
		$directive_id = $this->argument('id');
		$directive = $service->findById($directive_id);

		$this->table(
			array_keys($directive->getAttributes()),
			[
				$directive->getAttributes(),
			]
		);

		if ($this->confirm("Вы действительно хотите удалить Поручение?")) {
			$service->forceDelete($directive_id);
			$this->info("Поручение #{$directive_id} успешно удалено");
		}

	}
}
