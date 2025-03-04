<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\InteractionCancelledChangeExecutor;
use App\Modules\Processes\Events\InteractionCancelledDeadline;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\ChangeRequestToInWork;

class OnCancelledDeadline
{
	public function handle(
		InteractionCancelledDeadline $event,
		DirectiveService $service,
		ChangeRequestToInWork $changeRequestToInWork
	) {
		$process = $event->getProcess();
		$directive = $service->findById($process->document_id);

		$changeRequestToInWork->handle($directive);
	}
}