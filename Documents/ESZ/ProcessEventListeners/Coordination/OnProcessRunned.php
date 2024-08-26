<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Coordination;

use App\Modules\Processes\Events\ProcessRunned;
use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\FixToCoordination;
use SED\Documents\ESZ\Transitions\PreparationToCoordination;

class OnProcessRunned
{
	public function handle(
		ProcessRunned $event,
		ESZService $service,
		PreparationToCoordination $preparationToCoordination,
		FixToCoordination $fixToCoordination
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		if ($esz->isPreparation()) {
			$preparationToCoordination->execute($esz);
		} else if ($esz->isFix()) {
			$fixToCoordination->execute($esz);
		} else {
			throw new \LogicException('Предыдущий статус документа не соответствует логике!');
		}
	}
}
