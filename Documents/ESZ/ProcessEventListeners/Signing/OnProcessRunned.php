<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Signing;

use App\Modules\Processes\Events\ProcessRunned;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\FixSigningToSigning;

class OnProcessRunned
{
	public function handle(
		ProcessRunned $event,
		ESZService $service,
		FixSigningToSigning $fixSigningToSigning
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		if ($esz->isFixSigning()) {
			$fixSigningToSigning->execute($esz);
		} else if (!$esz->isSigning()) {
			throw new \LogicException("Текущий статус документа не соответствует логике! Текущий статус: {$esz->status_id}");
		}
	}
}
