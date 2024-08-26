<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Coordination;

use App\Modules\Processes\Events\ParticipantCancelledProcess;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\CoordinationToFix;

class OnParticipantCancelled
{
	public function handle(
		ParticipantCancelledProcess $event,
		ESZService $service,
		CoordinationToFix $coordinationToFix
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		$coordinationToFix->execute($esz);
	}
}
