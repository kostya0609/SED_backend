<?php
namespace SED\Documents\ESZ\Listeners;

use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Config\{CoordinationProcessConfig, SigningProcessConfig, ResolutionProcessConfig};
use SED\Documents\ESZ\Models\ESZ;
use App\Modules\Processes\Events\AddedActiveParticipant;
use App\Modules\Notification\Facades\NotificationFacade;

class AddActiveParticipantListener
{
	protected ESZService $service;

	public function __construct(ESZService $service)
	{
		$this->service = $service;
	}

	public function handle(AddedActiveParticipant $event)
	{
		$process = $event->getProcess();

		/** Идентификаторы шаблонов процессов в модуле */
		$templates = [
			CoordinationProcessConfig::getTemplateId(),
			SigningProcessConfig::getTemplateId(),
			ResolutionProcessConfig::getTemplateId(),
		];

		/** Если участник стал активным НЕ в нашем модуле, то завершаем обработку события */
		if (!in_array($process->template_id, $templates)) {
			return;
		}

		$document = $this->service->findById($process->document_id);
		$user_ids = $event->getParticipants()->map(fn($participant): int => $participant->user_id);

		NotificationFacade::sendFromBitrix($user_ids, $this->createMessage($document, $process->template_id));
	}

	protected function createMessage(ESZ $document, int $template_id): string
	{
		switch ($template_id) {
			case CoordinationProcessConfig::getTemplateId():
				return $this->createMessageForCoordinationProcess($document);
			
			case SigningProcessConfig::getTemplateId():
				return $this->createMessageForSigningProcess($document);	

			case ResolutionProcessConfig::getTemplateId():
				return $this->createMessageForResolutionProcess($document);

			default:
				throw new \LogicException("Не была реализована обработка для id шаблона процесса $template_id");
		}
	}

	protected function createMessageForCoordinationProcess(ESZ $document): string
	{
		$document_url = "https://bitrix.bsi.local/sed/documents/esz/detail/{$document->id}";

		$link = "[URL={$document_url}]$document->number[/URL]";

		return "Вам на согласование поступило ЭСЗ {$link} от {$document->initiator->user->full_name}, пожалуйста примите решение.";
	}

	protected function createMessageForSigningProcess(ESZ $document): string
	{
		$document_url = "https://bitrix.bsi.local/sed/documents/esz/detail/{$document->id}";

		$link = "[URL={$document_url}]$document->number[/URL]";

		return "Вам на подписание поступило ЭСЗ {$link} от {$document->initiator->user->full_name}, пожалуйста примите решение.";
	}

	protected function createMessageForResolutionProcess(ESZ $document): string
	{
		$document_url = "https://bitrix.bsi.local/sed/documents/esz/detail/{$document->id}";

		$link = "[URL={$document_url}]$document->number[/URL]";

		return "Вам на исполнение поступило ЭСЗ {$link} от {$document->initiator->user->full_name}, пожалуйста примите решение.";
	}
}
