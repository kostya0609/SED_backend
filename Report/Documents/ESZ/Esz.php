<?php
namespace SED\Report\Documents\ESZ;

use SED\Report\Components\FinedUser;
use SED\Report\Components\Link;
use SED\Report\Interfaces\Document;
use App\Modules\Processes\Facades\ParticipantFacade;

class Esz implements Document
{
	public Link $number;
	public string $created_at;
	public string $status_title;
	public string $initiator;
	public array $participants;
	public string $signatory;
	public array $receivers = [];
	public ?FinedUser $fine_initiator_on_approval = null;
	public ?FinedUser $fine_initiator_on_resolution = null;

	/**
	 * @var array<FinedUser>
	 */
	public array $fine_receivers_on_resolution = [];

	public function __construct(\SED\Documents\ESZ\Models\Esz $document)
	{
		$this->number = new Link($document->number, "https://bitrix.bsi.local/sed/documents/esz/detail/$document->id");
		$this->created_at = $document->created_at->format('Y-m-d H:i:s');
		$this->status_title = $document->status->title;
		$this->initiator = $document->initiator->user->full_name;

		$this->participants = ParticipantFacade::getParticipants($document->process_template_id, $document->id)->map(fn($participant) => $participant->user->full_name)->toArray();

		$this->signatory = $document->signatory->user->full_name;
		$this->receivers = $document->receivers->map(fn($receiver) => $receiver->user->full_name)->toArray();

		if ($document->isCoordination() || $document->isFix() || $document->isSigning() || $document->isFixSigning()) {
			$this->fine_initiator_on_approval = new FinedUser($document->initiator->user);
		}

		if ($document->isResolution() || $document->isFixResolution()) {
			$this->fine_initiator_on_resolution = new FinedUser($document->initiator->user);
		}

		if ($document->isResolution()) {
			$this->fine_receivers_on_resolution = $document
				->receivers
				->map(fn($receiver) => new FinedUser($receiver->user))
				->toArray();
		}
	}
}