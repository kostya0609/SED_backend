<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Resolution;

use App\Modules\Processes\Events\ProcessDecided;
use SED\Documents\ESZ\Dto\CreateProcessHistoryDto;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Services\ProcessHistoryService;

class OnProcessDecided
{
	public function handle(
		ProcessDecided $event,
		ESZService $service,
		ProcessHistoryService $processHistoryService
	) {
		$process = $event->getProcess();
		$user_id = $event->getUserId();
		$participant = $event->getParticipant();

		$history_dto = new CreateProcessHistoryDto();
		$history_dto->user_id = $user_id;
		$history_dto->esz_id = $process->document_id;
		$history_dto->event = 'Решение: ' . $participant->action->title;
		$history_dto->comment = $participant->comment;
		$history_dto->files = $participant->files;

		$processHistoryService->create($history_dto);
	}
}
