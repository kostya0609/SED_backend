<?php
namespace SED\Documents\Review\ProcessEventListeners;

use App\Modules\Processes\Events\ProcessCreated;
use App\Modules\Processes\Facades\ParticipantFacade;
use SED\Documents\Review\Config\DecideProcessConfig;
use SED\Documents\Review\Services\ReviewService;

class OnProcessCreated
{
	public function handle(ProcessCreated $event, ReviewService $service)
	{
		$process = $event->getProcess();
		$document_id = $process->document_id;

		ParticipantFacade::attachByTemplate(
			$process->id,
			DecideProcessConfig::getDecideGroupId(),
			$service->getReceivers($document_id)->toArray(),
		);
	}
}
