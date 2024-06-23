<?php
namespace SED\Documents\Directive\ProcessEventListeners\Execution;

use App\Modules\Processes\Events\ProcessDecided;
use SED\Documents\Directive\Dto\CreateProcessHistoryDto;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Services\ProcessHistoryService;

class OnProcessDecided
{
	public function handle(
		ProcessDecided $event,
		DirectiveService $service,
		ProcessHistoryService $processHistoryService
	) {
		$process = $event->getProcess();
		$user_id = $event->getUserId();
		$participant = $event->getParticipant();

		$history_dto = new CreateProcessHistoryDto();
		$history_dto->user_id = $user_id;
		$history_dto->directive_id = $process->document_id;
		$history_dto->event = 'Решение: ' . $participant->action->title;
		$history_dto->comment = $participant->comment;
		$history_dto->files = $participant->files;

		$processHistoryService->create($history_dto);
	}
}
