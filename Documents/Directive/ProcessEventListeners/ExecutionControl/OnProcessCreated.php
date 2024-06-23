<?php
namespace SED\Documents\Directive\ProcessEventListeners\ExecutionControl;

use App\Modules\Processes\Events\ProcessCreated;
use App\Modules\Processes\Facades\ParticipantFacade;
use SED\Documents\Directive\Config\ExecutionControlProcessConfig;
use SED\Documents\Directive\Config\ExecutionProcessConfig;
use SED\Documents\Directive\Services\DirectiveService;

class OnProcessCreated
{
	public function handle(ProcessCreated $event, DirectiveService $service)
	{
		$process = $event->getProcess();
		$document_id = $process->document_id;

		ParticipantFacade::attachByTemplate(
			$process->id,
			ExecutionControlProcessConfig::getControlGroupId(),
			$service->getControllers($document_id)->toArray()
		);
	}
}
