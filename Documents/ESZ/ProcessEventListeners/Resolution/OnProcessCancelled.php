<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Resolution;

use App\Modules\Processes\Events\ProcessCancelled;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\ResolutionToFixResolution;

class OnProcessCancelled
{
	public function handle(
		ProcessCancelled $event,
		ESZService $service,
		ResolutionToFixResolution $resolutionToFixResolution
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		$resolutionToFixResolution->execute($esz);
	}
}
