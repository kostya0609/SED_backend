<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Coordination;

use App\Modules\Processes\Events\ExecutorCancelledProcess;
use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\CoordinationToFix;
use SED\Documents\ESZ\Transitions\CoordinationToPreparation;

class OnExecutorCancelled
{
	public function handle(
		ExecutorCancelledProcess $event,
		ESZService $service,
		CoordinationToFix $coordinationToFix,
		CoordinationToPreparation $coordinationToPreparation
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		if ($esz->prev_status_id === Status::PREPARATION) {
			$coordinationToPreparation->execute($esz);
		} else if ($esz->prev_status_id === Status::FIX) {
			$coordinationToFix->execute($esz);
		} else {
			throw new \LogicException("Предыдущий статус документа не соответствует логике! Предыдущий статус: {$esz->prev_status_id}, текущий статус: {$esz->status_id}");
		}
	}
}
