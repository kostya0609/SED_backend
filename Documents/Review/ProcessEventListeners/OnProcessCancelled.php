<?php
namespace SED\Documents\Review\ProcessEventListeners;

use App\Modules\Processes\Events\ProcessCancelled;
use SED\Documents\Review\Services\ReviewService;
use SED\Documents\Review\Transitions\ReviewToPreparation;

class OnProcessCancelled
{
	public function handle(ProcessCancelled $event, ReviewToPreparation $reviewToPreparation, ReviewService $service )
	{
		$process = $event->getProcess();
		$document_id = $process->document_id;
		
		$review = $service->findById($document_id);
		
		$reviewToPreparation->handle($review);
	}
}
