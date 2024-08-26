<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Resolution;

use App\Modules\Processes\Events\ProcessCompleted;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\ResolutionToArchiveWorked;

class OnProcessCompleted
{
	public function handle(
		ProcessCompleted $event,
		ESZService $service,
		ResolutionToArchiveWorked $resolutionToArchiveWorked
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		$resolutionToArchiveWorked->execute($esz);
	}
}
