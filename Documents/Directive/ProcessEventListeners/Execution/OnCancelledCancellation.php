<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\InteractionCancelledCancellation;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\ChangeRequestToInWork;

class OnCancelledCancellation
{
	public function handle(
		InteractionCancelledCancellation $event,
		DirectiveService $service,
		ChangeRequestToInWork $changeRequestToInWork
	) {
		$process = $event->getProcess();
		$directive = $service->findById($process->document_id);

		$changeRequestToInWork->handle($directive);
	}
}