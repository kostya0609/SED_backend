<?php
namespace SED\Documents\ESZ\Commands;

use Illuminate\Console\Command;
use SED\Documents\ESZ\Services\ESZService;

class ESZForceDeleteCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-esz:force-delete {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Принудительное удаление ЭСЗ';

	public function handle(ESZService $service)
	{
		$esz_id = $this->argument('id');
		$esz = $service->findById($esz_id);

		$this->table(
			array_keys($esz->getAttributes()),
			[
				$esz->getAttributes()
			]
		);

		if ($this->confirm("Вы действительно хотите удалить ЭСЗ?")) {
			$service->forceDelete($esz_id);
			$this->info("ЭСЗ #{$esz_id} успешно удалено");
		}

	}
}
