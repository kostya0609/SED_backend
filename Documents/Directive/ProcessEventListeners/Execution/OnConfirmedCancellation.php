<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\InWorkToArchiveCancelled;
use App\Modules\Processes\Events\InteractionConfirmedCancellation;

class OnConfirmedCancellation
{
	public function handle(
		InteractionConfirmedCancellation $event,
		DirectiveService $service,
		InWorkToArchiveCancelled $inWorkToArchiveCancelled
	) {
		\DB::transaction(function () use ($event, $service, $inWorkToArchiveCancelled) {
			$process = $event->getProcess();
			$directive = $service->findById($process->document_id);

			ProcessFacade::reset($process->id);
			ProcessFacade::delete($process->id);

			$inWorkToArchiveCancelled->handle($directive);
		});
	}
}