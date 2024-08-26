<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Coordination;

use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use App\Modules\Processes\Events\ProcessCompleted;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\ESZ\Config\SigningProcessConfig;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Transitions\CoordinationToSigning;

class OnProcessCompleted
{
	public function handle(
		ProcessCompleted $event,
		ESZService $service,
		CoordinationToSigning $coordinationToSign
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$esz = $service->findById($document_id);

		$esz->process_template_id = SigningProcessConfig::getTemplateId();
		$esz = $coordinationToSign->execute($esz);


		ProcessFacade::create(
			CreateProcessDto::create(
				$esz->initiator->user_id,
				$esz->id,
				$esz->process_template_id,
				$esz->initiator->user_id
			)
		);
	}
}
