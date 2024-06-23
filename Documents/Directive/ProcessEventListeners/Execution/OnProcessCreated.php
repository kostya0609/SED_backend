<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\ProcessCreated;
use App\Modules\Processes\Facades\ParticipantFacade;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Config\ExecutionProcessConfig;

class OnProcessCreated
{
	public function handle(ProcessCreated $event, DirectiveService $service)
	{
		$process = $event->getProcess();
		$document_id = $process->document_id;

		ParticipantFacade::attachByTemplate(
			$process->id,
			ExecutionProcessConfig::getDecideGroupId(),
			$service->getExecutors($document_id)->toArray()
		);
	}
}
