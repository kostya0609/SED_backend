<?php
namespace SED\Documents\Directive\ProcessEventListeners\ExecutionControl;

use App\Modules\Processes\Events\ProcessCancelled;
use SED\Documents\Directive\Services\DirectiveService;

class OnProcessCancelled
{
	public function handle(
		ProcessCancelled $event,
		DirectiveService $service
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$directive = $service->findById($document_id);
	}
}
