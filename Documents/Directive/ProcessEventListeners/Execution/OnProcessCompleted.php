<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\ProcessCompleted;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\InWorkToControl;
use SED\Documents\Directive\Transitions\InWorkToArchiveWorked;

class OnProcessCompleted
{
	public function handle(
		ProcessCompleted $event,
		DirectiveService $service,
		InWorkToArchiveWorked $inWorkToArchiveWorked,
		InWorkToControl $inWorkToControl
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$directive = $service->findById($document_id);

		if ($directive->controllers->isEmpty()) {
			$inWorkToArchiveWorked->handle($directive);
		} else {
			$inWorkToControl->handle($directive);
		}
	}
}
