<?php
namespace SED\Documents\Review\ProcessEventListeners;

use App\Modules\Processes\Events\ProcessCompleted;
use SED\Documents\Review\Services\ReviewService;
use SED\Documents\Review\Transitions\ReviewToArchiveWorked;
use App\Modules\Notification\Facades\NotificationFacade;

class OnProcessCompleted
{
	public function handle(ProcessCompleted $event, ReviewService $service, ReviewToArchiveWorked $reviewToArchiveWorked) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$review = $service->findById($document_id);

		$reviewToArchiveWorked->handle($review);

		$document_url = "https://bitrix.bsi.local/sed/documents/review/detail/{$document_id}";

		$link = "[URL={$document_url}]$review->number[/URL]";

		NotificationFacade::sendFromBitrix($review->initiator->user_id, "Все участники ознакомились с документом {$link}.");
	}
}
