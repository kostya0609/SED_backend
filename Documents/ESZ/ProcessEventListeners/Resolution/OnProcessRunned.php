<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Resolution;

use App\Modules\Processes\Events\ProcessRunned;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\FixResolutionToResolution;

class OnProcessRunned
{
	public function handle(
		ProcessRunned $event,
		ESZService $service,
		FixResolutionToResolution $fixResolutionToResolution
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		if ($esz->isFixResolution()) {
			$fixResolutionToResolution->execute($esz);
		} else if (!$esz->isResolution()) {
			throw new \LogicException('Текущий статус документа не соответствует логике!');
		}
	}
}
