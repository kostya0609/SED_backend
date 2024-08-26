<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Directive\Models\Directive;
use SED\Documents\Directive\Dto\CreateHistoryDto;
use SED\Documents\Directive\Services\HistoryService;

abstract class BaseTransition
{
	protected HistoryService $historyService;
	protected DocumentService $documentService;

	public function __construct(HistoryService $historyService, DocumentService $documentService)
	{
		$this->historyService = $historyService;
		$this->documentService = $documentService;
	}

	abstract public function handle(Directive $directive): Directive;

	protected function execute(Directive $directive)
	{
		$history = new CreateHistoryDto();
		$history->directive_id = $directive->id;
		$history->user_id = $directive->creator->user_id;
		$history->event = "Поручение переведено в статус \"{$directive->status->title}\"";
		$this->historyService->create($history);

		$document_dto = new UpdateDocumentDto();
		$document_dto->theme = $directive->theme;
		$document_dto->initiator_id = $directive->creator->user_id;
		$document_dto->status_title = $directive->status->title;
		$this->documentService->update($directive->id, $directive->type_id, $document_dto);

		return $directive;
	}
}