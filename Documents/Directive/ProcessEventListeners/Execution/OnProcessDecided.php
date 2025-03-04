<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\ProcessDecided;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Dto\CreateProcessHistoryDto;
use SED\Documents\Directive\Config\ExecutionProcessConfig;
use SED\Documents\Directive\Services\ProcessHistoryService;
use SED\Documents\Directive\Transitions\InWorkToChangeRequest;

class OnProcessDecided
{
	public function handle(
		ProcessDecided $event,
		DirectiveService $service,
		ProcessHistoryService $processHistoryService,
		InWorkToChangeRequest $inWorkToChangeRequest
	) {
		\DB::transaction(function () use ($event, $service, $processHistoryService, $inWorkToChangeRequest) {
			$process = $event->getProcess();
			$user_id = $event->getUserId();
			$participant = $event->getParticipant();
			$directive = $service->findById($process->document_id);

			if (
				in_array($participant->action->id, [
					ExecutionProcessConfig::getRequestCancellationActionId(),
					ExecutionProcessConfig::getRequestChangeDeadlineActionId(),
					ExecutionProcessConfig::getRequestChangeExecutorActionId(),
				])
			) {
				$inWorkToChangeRequest->handle($directive);
			}

			$history_dto = new CreateProcessHistoryDto();
			$history_dto->user_id = $user_id;
			$history_dto->subuser_id = $participant->subuser->id ?? null;
			$history_dto->directive_id = $process->document_id;
			$history_dto->process_template_name = $process->template->title;
			$history_dto->event = 'Решение: ' . $participant->action->title;
			$history_dto->comment = $participant->comment;
			$history_dto->files = $participant->files;

			$processHistoryService->create($history_dto);
		});
	}
}
