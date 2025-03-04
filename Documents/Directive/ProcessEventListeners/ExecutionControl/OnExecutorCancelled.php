<?php
namespace SED\Documents\Directive\ProcessEventListeners\ExecutionControl;

use App\Modules\Processes\Facades\ProcessFacade;
use App\Modules\Processes\Events\ExecutorCancelledProcess;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\ControlToArchiveCancelled;

class OnExecutorCancelled
{
	public function handle(
		ExecutorCancelledProcess $event,
		DirectiveService $service,
		ControlToArchiveCancelled $controlToArchiveCancelled
	) {
		\DB::transaction(function () use ($event, $service, $controlToArchiveCancelled) {
			$process = $event->getProcess();
			$document_id = $process->document_id;
			$directive = $service->findById($document_id);

			ProcessFacade::delete($process->id);

			$controlToArchiveCancelled->handle($directive);
		});
	}
}