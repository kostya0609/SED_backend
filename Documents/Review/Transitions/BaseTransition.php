<?php
namespace SED\Documents\Review\Transitions;

use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Review\Models\Review;
use SED\Documents\Review\Dto\CreateHistoryDto;
use SED\Documents\Review\Services\HistoryService;

abstract class BaseTransition
{
	protected HistoryService $historyService;
	protected DocumentService $documentService;

	public function __construct(HistoryService $historyService, DocumentService $documentService)
	{
		$this->historyService = $historyService;
		$this->documentService = $documentService;
	}
	
	abstract public function handle(Review $review): Review;

	protected function execute(Review $review)
	{
		$history = new CreateHistoryDto();
		$history->review_id = $review->id;
		$history->user_id = $review->responsible->user_id;
		$history->event = "Ознакомление переведено в статус \"{$review->status->title}\"";
		$this->historyService->create($history);

		$document_dto = new UpdateDocumentDto();
		$document_dto->number = $review->number;
		$document_dto->theme = $review->theme->title;
		$document_dto->executor_id = $review->responsible->user_id;
		$document_dto->status_title = $review->status->title;
		$this->documentService->update($review->id, $review->type_id, $document_dto);

		return $review;
	}
}
