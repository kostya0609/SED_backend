<?php
namespace SED\Documents\Review\Commands;

use Illuminate\Console\Command;
use SED\Documents\Review\Services\ReviewService;

class ReviewForceDeleteCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sed-review:force-delete {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Принудительное удаление Ознакомления';

	public function handle(ReviewService $service)
	{
		$review_id = $this->argument('id');
		$review = $service->findById($review_id);

		$this->table(
			array_keys($review->getAttributes()),
			[
				$review->getAttributes(),
			]
		);

		if ($this->confirm("Вы действительно хотите удалить Ознакомление?")) {
			$service->forceDelete($review_id);
			$this->info("Ознакомление #{$review_id} успешно удалено");
		}

	}
}
