<?php
namespace SED\Documents\Common\Services\BasedCreation\Creators;

use SED\Documents\Common\Models\Document;
use SED\Documents\ESZ\Dto\PreCreateESZDto;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\Common\Services\BasedCreation\BasedCreationInterface;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

class CreatorBasedOnEszTemplate implements BasedCreationInterface
{
	private ESZService $service;

	public function __construct(ESZService $service)
	{
		$this->service = $service;
	}

	public function create(Document $base_document, DocumentTemplate $template, ?int $initiator_id = null): Document
	{
		$dto = new PreCreateESZDto();
		$dto->content = $template['data']->content;
		$dto->portfolio = '';
		$dto->user_id = $initiator_id ?: $base_document->initiator_id;
		$dto->tmp_doc_id = $template->id;
		$dto->parent_document_id = $base_document->id;
		$dto->theme_title = null;

		if ($template['data']->signatory) {
			$dto->setSignatory((array) $template['data']->signatory);
		}

		foreach ($template['data']->receivers as $receivers) {
			$dto->addReceiver((array) $receivers);
		}

		foreach ($template['data']->observers as $observer) {
			$dto->addObserver((array) $observer);
		}

		$esz = $this->service->preCreate($dto);

		return $esz->commonDocument;
	}
}