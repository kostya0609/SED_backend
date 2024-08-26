<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Resolution;

use App\Modules\Processes\Events\ProcessCreated;
use App\Modules\Processes\Facades\ParticipantFacade;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\ESZ\Config\ResolutionProcessConfig;
use SED\Documents\ESZ\Services\ESZService;

class OnProcessCreated
{
	public function handle(ProcessCreated $event, ESZService $service)
	{
		$process = $event->getProcess();
		$user_id = $event->getUserId();
		$esz = $service->findById($process->document_id);

		$receivers = $esz->receivers->pluck('user_id')->values()->toArray();

		ParticipantFacade::attachByTemplate(
			$process->id,
			ResolutionProcessConfig::getResolutionGroupId(),
			$receivers
		);

		ProcessFacade::run($process->id, $user_id);
	}
}
