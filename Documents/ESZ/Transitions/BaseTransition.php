<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Models\ESZ;
use SED\Documents\ESZ\Dto\CreateHistoryDto;
use SED\Documents\ESZ\Services\HistoryService;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Services\DocumentService;

abstract class BaseTransition
{
	protected HistoryService $historyService;
	protected DocumentService $documentService;

	public function __construct(HistoryService $historyService, DocumentService $documentService)
	{
		$this->historyService = $historyService;
		$this->documentService = $documentService;
	}

	abstract protected function getFromStatusId(): int;
	abstract protected function getToStatusId(): int;

	public function execute(ESZ $esz): ESZ
	{
		return \DB::transaction(function () use ($esz): ESZ {
			$esz = $this->beforeHandle($esz);
			$esz = $this->handle($esz);

			$esz->save();
			$esz = $esz->fresh(['status']);

			$esz = $this->afterHandle($esz);

			return $esz;
		});
	}

	protected function beforeHandle(ESZ $esz): ESZ
	{
		$esz->prev_status_id = $esz->status_id;
		return $esz;
	}

	protected function handle(ESZ $esz): ESZ
	{
		if ($esz->status_id !== $this->getFromStatusId()) {
			throw new \Exception("Неверный статус ЭСЗ для данного перехода! Текущий статус $esz->status_id, ожидаемый статус {$this->getFromStatusId()}");
		}

		$esz->status_id = $this->getToStatusId();
		return $esz;
	}

	protected function afterHandle(ESZ $esz): ESZ
	{
		$history = new CreateHistoryDto();
		$history->esz_id = $esz->id;
		$history->user_id = $esz->initiator->user_id;
		$history->event = "ЭСЗ переведено в статус \"{$esz->status->title}\"";
		$this->historyService->create($history);

		$document_dto = new UpdateDocumentDto();
		$document_dto->theme = $esz->theme->title;
		$document_dto->initiator_id = $esz->initiator->user_id;
		$document_dto->status_title = $esz->status->title;
		$this->documentService->update($esz->id, $esz->type_id, $document_dto);

		return $esz;
	}
}
