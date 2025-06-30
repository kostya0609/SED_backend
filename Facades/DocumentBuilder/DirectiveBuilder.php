<?php
namespace SED\Facades\DocumentBuilder;

use SED\Documents\Directive\Models\Directive;
use SED\Documents\Common\Dto\UserRoleAggregator;
use SED\Documents\Directive\Dto\PreCreateDirectiveDto;
use SED\Documents\Directive\Services\DirectiveService;

class DirectiveBuilder extends AbstractDocumentBuilder
{
	private PreCreateDirectiveDto $dto;
	private DirectiveService $service;

	protected function init(): void
	{
		$this->dto = new PreCreateDirectiveDto();
		$this->service = \App::make(DirectiveService::class);
	}

	protected function fillDto(): void
	{
		$template = $this->getTemplate();

		if (!$template->isDirective()) {
			throw new \LogicException("Шаблон #{$this->getTemplateId()} не является поручением!");
		}

		$days_amount = empty($template['data']->days_amount) ? 0 : (int) $template['data']->days_amount;

		if (!is_int($days_amount)) {
			throw new \LogicException('Invalid days amount');
		}

		$this->dto->executed_at = \Carbon\Carbon::now()->addDays($days_amount);
		$this->dto->content = $template['data']->content;
		$this->dto->portfolio = '';
		$this->dto->theme_title = null;

		if ($template['data']->author) {
			$this->dto->setAuthor((array) $template['data']->author);
		}

		foreach ($template['data']->executors as $executor) {
			$this->dto->addExecutor((array) $executor);
		}

		foreach ($template['data']->controllers as $controller) {
			$this->dto->addController((array) $controller);
		}

		foreach ($template['data']->observers as $observer) {
			$this->dto->addObserver((array) $observer);
		}

		$this->dto->tmp_doc_id = $template->id;
	}

	public function setParentDocumentId(?int $parent_document_id)
	{
		$this->dto->document_hierarchy_id = $parent_document_id;
		parent::setParentDocumentId($parent_document_id);
		return $this;
	}

	public function setExecutedAt(\Carbon\Carbon $executed_at)
	{
		$this->dto->executed_at = $executed_at->toDateTimeString();
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

	public function setCreatorId(int $creator_id)
	{
		$this->dto->creator_id = $creator_id;
		return $this;
	}

	public function setAuthor(UserRoleAggregator $author)
	{
		$this->dto->author = $author;
		return $this;
	}

	public function clearExecutors()
	{
		$this->dto->executors = collect([]);
		return $this;
	}

	public function addExecutor(UserRoleAggregator $executor)
	{
		$this->dto->executors->push($executor);
		return $this;
	}

	public function clearControllers()
	{
		$this->dto->controllers = collect([]);
		return $this;
	}

	public function addController(UserRoleAggregator $controller)
	{
		$this->dto->controllers->push($controller);
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

	protected function validate(): void
	{
		if (!isset($this->dto->creator_id)) {
			throw new \LogicException('Не был установлен идентификатор создателя! Установить его можно с помощью setCreatorId(int $creator_id)');
		}

		if (!isset($this->dto->executed_at)) {
			throw new \LogicException('Не была установлена дата исполнения поручения! Установить можно с помощью setExecutedAt(\Carbon\Carbon $executed_at)');
		}

		if (!isset($this->dto->content)) {
			throw new \LogicException('Не было установлено содержимое документа! Установить его можно с помощью setContent(string $content)');
		}

		if (!$this->getTemplate() && !isset($this->dto->theme_title)) {
			throw new \LogicException('Не была установлена тема документа! Установить его можно с помощью setThemeTitle(string $theme_title)');
		}
	}

	public function save(): Directive
	{
		$this->validate();
		return $this->service->preCreate($this->dto);
	}
}