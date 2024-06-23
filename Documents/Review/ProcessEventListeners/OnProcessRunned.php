<?php
namespace SED\Documents\Review\ProcessEventListeners;

use App\Modules\Processes\Events\ProcessRunned;
use SED\Documents\Review\Services\ReviewService;
use SED\Documents\Review\Transitions\PreparationToReview;

class OnProcessRunned
{
	public function handle(ProcessRunned $event, PreparationToReview $preparationToReview, ReviewService $service )
	{
		$process = $event->getProcess();
		$document_id = $process->document_id;
		
		$review = $service->findById($document_id);
		
		$preparationToReview->handle($review);
	}
}
