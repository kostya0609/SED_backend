<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Facades\ParticipantFacade;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Transitions\ChangeRequestToInWork;
use App\Modules\Processes\Events\InteractionConfirmedChangeExecutor;

class OnConfirmedChangeExecutor
{
	public function handle(
		InteractionConfirmedChangeExecutor $event,
		DirectiveService $service,
		ChangeRequestToInWork $changeRequestToInWork
	) {
		\DB::transaction(function () use ($event, $service, $changeRequestToInWork) {
			$process = $event->getProcess();
			$directive = $service->findById($process->document_id);
			$current_executor_id = $event->getCurrentExecutorId();
			$new_executor_id = $event->getNewExecutorId();
			$participant_id = $event->getParticipantId();

			$directive->executors()->where('user_id', $current_executor_id)->update(['user_id' => $new_executor_id]);
			ParticipantFacade::replaceParticipant($process, $participant_id, $new_executor_id);

			$changeRequestToInWork->handle($directive);
		});
	}
}