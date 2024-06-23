<?php
namespace SED\Documents\Directive\Listeners;

use SED\Documents\Directive\Models\Directive;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Directive\Config\ExecutionProcessConfig;
use App\Modules\Processes\Events\AddedActiveParticipant;
use SED\Documents\Directive\Config\ExecutionControlProcessConfig;
use App\Modules\Notification\Facades\NotificationFacade;

class AddActiveParticipantListener
{
	protected DirectiveService $service;

	public function __construct(DirectiveService $service)
	{
		$this->service = $service;
	}

	public function handle(AddedActiveParticipant $event)
	{
		$process = $event->getProcess();

		/** Идентификаторы шаблонов процессов в модуле */
		$templates = [
			ExecutionProcessConfig::getTemplateId(),
			ExecutionControlProcessConfig::getTemplateId(),
		];

		/** Если участник стал активным НЕ в нашем модуле, то завершаем обработку события */
		if (!in_array($process->template_id, $templates)) {
			return;
		}

		$document = $this->service->findById($process->document_id);
		$user_ids = $event->getParticipants()->map(fn($participant): int => $participant->user_id);

		NotificationFacade::sendFromBitrix($user_ids, $this->createMessage($document, $process->template_id));
	}

	protected function createMessage(Directive $document, int $template_id): string
	{
		switch ($template_id) {
			case ExecutionProcessConfig::getTemplateId():
				return $this->createMessageForDecideProcess($document);
			case ExecutionControlProcessConfig::getTemplateId():
				return $this->createMessageForControlProcess($document);

			default:
				throw new \LogicException("Не была реализована обработка для id шаблона процесса $template_id");
		}
	}

	protected function createMessageForDecideProcess(Directive $document): string
	{
		$document_url = "https://bitrix.bsi.local/sed/documents/directive/detail/{$document->id}";

		$link = "[URL={$document_url}]$document->number[/URL]";

		return "Вам на исполнение поступило Поручение {$link} от {$document->creator->user->full_name}, пожалуйста примите решение.";
	}

	protected function createMessageForControlProcess(Directive $document): string
	{
		$document_url = "https://bitrix.bsi.local/sed/documents/directive/detail/{$document->id}";

		$link = "[URL={$document_url}]$document->number[/URL]";

		return "Вам на контроль поступило поступило Поручение {$link} от {$document->creator->user->full_name}, пожалуйста примите решение.";
	}
}
