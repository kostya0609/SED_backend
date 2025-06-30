<?php
namespace SED\Documents\Common\Services\BasedCreation\Creators;

use SED\Documents\Common\Models\Document;
use SED\Documents\Review\Dto\PreCreateReviewDto;
use SED\Documents\Review\Services\ReviewService;
use SED\Documents\Common\Services\BasedCreation\BasedCreationInterface;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

class CreatorBasedOnReviewTemplate implements BasedCreationInterface
{
	private ReviewService $service;

	public function __construct(ReviewService $service)
	{
		$this->service = $service;
	}

	public function create(Document $base_document, DocumentTemplate $template, ?int $initiator_id = null): Document
	{
		$dto = new PreCreateReviewDto();
		$dto->content = $template['data']->content;
		$dto->portfolio = '';
		$dto->tmp_doc_id = $template->id;
		$dto->user_id = $initiator_id ?: $base_document->initiator_id;
		$dto->parent_document_id = $base_document->id;
		$dto->theme_title = null;
		$dto->document_hierarchy_id = $base_document->document_hierarchy_id;
        $dto->root_tmp_id = $base_document->root_tmp_id;

		foreach ($template['data']->receivers as $receiver) {
			$dto->addReceiver((array) $receiver);
		}

		$review = $this->service->preCreate($dto);

		return $review->commonDocument;
	}
}
