<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\ProcessRunned;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\PreparationToInWork;

class OnProcessRunned
{
	public function handle(ProcessRunned $event, PreparationToInWork $preparationToInWork, DirectiveService $service)
	{
		$process = $event->getProcess();
		$document_id = $process->document_id;
		$directive = $service->findById($document_id);
		
		$preparationToInWork->handle($directive);
	}
}
