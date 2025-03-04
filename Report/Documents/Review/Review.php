<?php
namespace SED\Report\Documents\Review;

use SED\Report\Components\Link;
use SED\Report\Interfaces\Document;
use SED\Report\Components\FinedUser;
use App\Modules\Processes\Facades\ParticipantFacade;

class Review implements Document
{
	public Link $number;
	public string $created_at;
	public string $status_title;
	public string $initiator;

	/**
	 * @var array<FinedUser>
	 */
	public array $fine_receivers_on_review = [];

	public function __construct(\SED\Documents\Review\Models\Review $document)
	{
		$this->number = new Link($document->number, "https://bitrix.bsi.local/sed/documents/directive/detail/$document->id");
		$this->created_at = $document->created_at->format('Y-m-d H:i:s');
		$this->status_title = $document->status->title;
		$this->initiator = $document->initiator->user->full_name;

		if ($document->isReview()) {
			$this->fine_receivers_on_review = ParticipantFacade::getParticipantsHasNotDecided($document->process_template_id, $document->id)
				->map(fn($participant) => new FinedUser($participant->user))
				->toArray();
		}
	}
}