<?php
namespace SED\Documents\Review\Listeners;

use App\Modules\Notification\Facades\NotificationFacade;
use App\Modules\Processes\Events\AddedActiveParticipant;
use SED\Documents\Review\Config\DecideProcessConfig;
use SED\Documents\Review\Models\Review;
use SED\Documents\Review\Services\ReviewService;

class AddActiveParticipantListener
{

	protected  ReviewService $service;

	public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }


	public function handle(AddedActiveParticipant $event)
	{
		$process = $event->getProcess();

		/** Идентификаторы шаблонов процессов в модуле */
		$templates = [
			DecideProcessConfig::getProcessTemplateId(),
		];

		/** Если участник стал активным НЕ в нашем модуле, то завершаем обработку события */
		if (!in_array($process->template_id, $templates)) {
			return;
		}

		$document = $this->service->findById($process->document_id);
		$user_ids = $event->getParticipants()->map(fn($participant): int => $participant->user_id);

		NotificationFacade::sendFromBitrix($user_ids, $this->createMessage($document));
	}

	protected function createMessage(Review $document): string
	{
		$document_url = "https://bitrix.bsi.local/sed/documents/review/detail/{$document->id}";

		$link = "[URL={$document_url}]$document->number[/URL]";

		return "Вам на исполнение поступило Ознакомление {$link} от {$document->initiator->user->full_name}, пожалуйста примите решение.";
	}
}
