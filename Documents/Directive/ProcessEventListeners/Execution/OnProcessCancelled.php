<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\ProcessCancelled;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\InWorkToPreparation;

class OnProcessCancelled
{
	public function handle(
		ProcessCancelled $event,
		InWorkToPreparation $inWorkToPreparation,
		DirectiveService $service
	) {
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$directive = $service->findById($document_id);

		$inWorkToPreparation->handle($directive);
	}
}
