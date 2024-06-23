<?php
namespace SED\Documents\Directive\ProcessEventListeners\ExecutionControl;

use App\Modules\Processes\Events\ProcessCompleted;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\ControlToArchiveWorked;

class OnProcessCompleted
{
	public function handle(
		ProcessCompleted $event,
		DirectiveService $service,
		ControlToArchiveWorked $controlToArchiveWorked
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$directive = $service->findById($document_id);

		$controlToArchiveWorked->handle($directive);
	}
}
