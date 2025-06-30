<?php
namespace SED\Facades\DocumentBuilder;

use SED\Documents\Review\Models\Review;
use SED\Documents\Common\Dto\UserRoleAggregator;
use SED\Documents\Review\Services\ReviewService;
use SED\Documents\Review\Dto\PreCreateReviewDto;

class ReviewBuilder extends AbstractDocumentBuilder
{
	private PreCreateReviewDto $dto;
	private ReviewService $service;

	protected function init(): void
	{
		$this->dto = new PreCreateReviewDto();
		$this->service = \App::make(ReviewService::class);
	}

	protected function fillDto(): void
	{
		$template = $this->getTemplate();

		if (!$template->isReview()) {
			throw new \LogicException("Шаблон #{$this->getTemplateId()} не является ознакомлением!");
		}

		$this->dto->content = $template['data']->content;
		$this->dto->portfolio = '';
		$this->dto->tmp_doc_id = $template->id;
		$this->dto->theme_title = null;

		foreach ($template['data']->receivers as $receiver) {
			$this->dto->addReceiver((array) $receiver);
		}
	}

	public function setParentDocumentId(?int $parent_document_id)
	{
		$this->dto->document_hierarchy_id = $parent_document_id;
		parent::setParentDocumentId($parent_document_id);
		return $this;
	}

	public function setInitiatorId(int $user_id)
	{
		$this->dto->user_id = $user_id;
		return $this;
	}

	public function setContent(string $content)
	{
		$this->dto->content = $content;
		return $this;
	}

	public function setPortfolio(string $portfolio)
	{
		$this->dto->portfolio = $portfolio;
		return $this;
	}

	public function clearReceivers()
	{
		$this->dto->receivers = collect([]);
		return $this;
	}

	public function addReceiver(UserRoleAggregator $receiver)
	{
		$this->dto->receivers->push($receiver);
		return $this;
	}

	public function setThemeTitle(?string $theme_title)
	{
		$this->dto->theme_title = $theme_title;
		return $this;
	}

	protected function validate()
	{
		if (!isset($this->dto->user_id) || !$this->dto->user_id) {
			throw new \LogicException('Не был установлен идентификатор инициатора! Установить его можно с помощью setInitiatorId(int $user_id)');
		}

		if (!isset($this->dto->content)) {
			throw new \LogicException('Не было установлено содержимое документа! Установить его можно с помощью setContent(string $content)');
		}

		if (!$this->getTemplate() && !isset($this->dto->theme_title)) {
			throw new \LogicException('Не была установлена тема документа! Установить его можно с помощью setThemeTitle(string $theme_title)');
		}
	}

	public function save(): Review
	{
		$this->validate();
		return $this->service->preCreate($this->dto);
	}
}