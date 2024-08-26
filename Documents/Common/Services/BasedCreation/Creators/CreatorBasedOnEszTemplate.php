<?php
namespace SED\Documents\Common\Services\BasedCreation\Creators;

use SED\Documents\Common\Models\Document;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\ESZ\Dto\CreateUpdateESZDto;
use SED\Documents\Common\Services\BasedCreation\BasedCreationInterface;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

class CreatorBasedOnEszTemplate implements BasedCreationInterface
{
	private ESZService $esz_service;

	public function __construct(ESZService $esz_service)
	{
		$this->esz_service = $esz_service;
	}

	public function create(Document $base_document, DocumentTemplate $esz_template): Document
	{
		$dto = new CreateUpdateESZDto();
		$dto->content = 'Content';
		$dto->portfolio = 'Portfolio';
		$dto->signatory_id = 14956;
		$dto->receivers = [15001, 15002];
		$dto->observers = [15003, 15004];
		$dto->user_id = 14956;
		$dto->tmp_doc_id = $esz_template->id;

		$esz = $this->esz_service->create($dto);

		return $esz->commonDocument;
	}
}