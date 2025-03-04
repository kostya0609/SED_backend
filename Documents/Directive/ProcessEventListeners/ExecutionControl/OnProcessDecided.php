<?php
namespace SED\Documents\Directive\ProcessEventListeners\ExecutionControl;

use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use App\Modules\Processes\Events\ProcessDecided;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\Directive\Config\ExecutionControlProcessConfig;
use SED\Documents\Directive\Config\ExecutionProcessConfig;
use SED\Documents\Directive\Dto\CreateProcessHistoryDto;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Services\ProcessHistoryService;
use SED\Documents\Directive\Transitions\ControlToInWork;

class OnProcessDecided
{
	public function handle(
		ProcessDecided $event,
		DirectiveService $service,
		ProcessHistoryService $processHistoryService,
		ControlToInWork $controlToInWork
	) {
		\DB::transaction(function () use ($event, $service, $processHistoryService, $controlToInWork) {
			$process = $event->getProcess();
			$user_id = $event->getUserId();
			$participant = $event->getParticipant();
			$directive = $service->findById($process->document_id);

			if ($participant->action->id === ExecutionControlProcessConfig::getReturnToExecutorActionId()) {
				ProcessFacade::delete($process->id);

				$directive->process_template_id = ExecutionProcessConfig::getTemplateId();

				$controlToInWork->handle($directive);

				$active_process = ProcessFacade::create(
					CreateProcessDto::create(
						$directive->author->user_id,
						$directive->id,
						$directive->process_template_id,
						$directive->creator->user_id
					)
				);

				ProcessFacade::run($active_process->process->id, $directive->author->user_id);
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
