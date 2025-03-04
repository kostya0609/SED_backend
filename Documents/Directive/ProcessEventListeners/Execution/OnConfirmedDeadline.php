<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use Carbon\Carbon;
use SED\Documents\Directive\Services\DirectiveService;
use App\Modules\Processes\Events\InteractionConfirmedDeadline;
use SED\Documents\Directive\Transitions\ChangeRequestToInWork;

class OnConfirmedDeadline
{
	public function handle(
		InteractionConfirmedDeadline $event,
		DirectiveService $service,
		ChangeRequestToInWork $changeRequestToInWork
	) {
		$process = $event->getProcess();
		$directive = $service->findById($process->document_id);
		$deadline_timestamp = $event->getDeadlineTimestamp();
		$deadline = Carbon::createFromTimestampMs($deadline_timestamp, \DateTimeZone::ASIA);

		$directive->executed_at = $deadline->format('Y-m-d');
		$changeRequestToInWork->handle($directive);
	}
}