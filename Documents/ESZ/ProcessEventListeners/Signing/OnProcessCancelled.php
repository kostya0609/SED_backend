<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Signing;

use App\Modules\Processes\Events\ProcessCancelled;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\SigningToFixSigning;

class OnProcessCancelled
{
	public function handle(
		ProcessCancelled $event,
		ESZService $service,
		SigningToFixSigning $signingToFixSigning
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		$signingToFixSigning->execute($esz);
	}
}
