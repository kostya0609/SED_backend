<?php
namespace SED\Documents\Common\Services\BasedCreation\Creators;

use Carbon\Carbon;
use SED\Documents\Common\Models\Document;
use SED\Documents\Directive\Dto\PreCreateDirectiveDto;
use SED\Documents\Directive\Services\DirectiveService;
use SED\Documents\Common\Services\BasedCreation\BasedCreationInterface;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

class CreatorBasedOnDirectiveTemplate implements BasedCreationInterface
{
	private DirectiveService $service;

	public function __construct(DirectiveService $service)
	{
		$this->service = $service;
	}

	public function create(Document $base_document, DocumentTemplate $template, ?int $initiator_id = null): Document
	{
		$days_amount = empty($template['data']->days_amount) ? 0 : (int) $template['data']->days_amount;

		if (!is_int($days_amount)) {
			throw new \LogicException('Invalid days amount');
		}

		$dto = new PreCreateDirectiveDto();
		$dto->executed_at = Carbon::now()->addDays($days_amount);
		$dto->content = $template['data']->content;
		$dto->portfolio = '';
		$dto->theme_title = null;
		$dto->creator_id = $initiator_id ?: $base_document->initiator_id;

		if ($template['data']->author) {
			$dto->setAuthor((array) $template['data']->author);
		}

		foreach ($template['data']->executors as $executor) {
			$dto->addExecutor((array) $executor);
		}

		foreach ($template['data']->controllers as $controller) {
			$dto->addController((array) $controller);
		}

		foreach ($template['data']->observers as $observer) {
			$dto->addObserver((array) $observer);
		}

		$dto->tmp_doc_id = $template->id;
		$dto->parent_document_id = $base_document->id;

		$directive = $this->service->preCreate($dto);

		return $directive->commonDocument;
	}
}