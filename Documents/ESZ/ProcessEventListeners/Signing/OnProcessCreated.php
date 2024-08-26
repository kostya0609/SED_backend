<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Signing;

use App\Modules\Processes\Events\ProcessCreated;
use App\Modules\Processes\Facades\ParticipantFacade;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\ESZ\Config\SigningProcessConfig;
use SED\Documents\ESZ\Services\ESZService;

class OnProcessCreated
{
	public function handle(ProcessCreated $event, ESZService $service)
	{
		$process = $event->getProcess();
		$user_id = $event->getUserId();
		$esz = $service->findById($process->document_id);

		$signatory = $esz->signatory;

		ParticipantFacade::attachByTemplate(
			$process->id,
			SigningProcessConfig::getSigningGroupId(),
			[$signatory->user_id]
		);

		ProcessFacade::run($process->id, $user_id);
	}
}
