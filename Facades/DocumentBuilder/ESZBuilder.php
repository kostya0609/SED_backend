<?php
namespace SED\Facades\DocumentBuilder;

use SED\Documents\ESZ\Models\Esz;
use SED\Documents\ESZ\Dto\PreCreateESZDto;
use SED\Documents\ESZ\Services\ESZService;
use SED\Documents\Common\Dto\UserRoleAggregator;

class ESZBuilder extends AbstractDocumentBuilder
{
	private PreCreateESZDto $dto;
	private ESZService $service;

	protected function init(): void
	{
		$this->dto = new PreCreateESZDto();
		$this->service = \App::make(ESZService::class);
	}
	protected function fillDto(): void
	{
		$template = $this->getTemplate();

		if (!$template->isESZ()) {
			throw new \LogicException("Шаблон #{$this->getTemplateId()} не является ЭСЗ!");
		}

		$this->dto->content = $template['data']->content;
		$this->dto->portfolio = '';
		$this->dto->tmp_doc_id = $template->id;
		$this->dto->theme_title = null;

		if ($template['data']->signatory) {
			$this->dto->setSignatory((array) $template['data']->signatory);
		}

		foreach ($template['data']->receivers as $receivers) {
			$this->dto->addReceiver((array) $receivers);
		}

		foreach ($template['data']->observers as $observer) {
			$this->dto->addObserver((array) $observer);
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

	public function setSignatory(UserRoleAggregator $signatory)
	{
		$this->dto->signatory = $signatory;
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

	public function clearObservers()
	{
		$this->dto->observers = collect([]);
		return $this;
	}

	public function addObserver(UserRoleAggregator $observer)
	{
		$this->dto->observers->push($observer);
		return $this;
	}

	public function setThemeTitle(string $theme_title)
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

	public function save(): Esz
	{
		$this->validate();
		return $this->service->preCreate($this->dto);
	}
}