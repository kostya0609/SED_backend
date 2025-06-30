<?php
namespace SED\Facades\DocumentBuilder;

use SED\DocumentRoutes\DocumentTemplate;

abstract class AbstractDocumentBuilder
{
	private ?int $template_id = null;
	private ?int $parent_document_id = null;
	private ?DocumentTemplate $template = null;

	/**
	 * Создает произвленный документ без указания шаблона
	 * @return static
	 */
	public function createEmpty()
	{
		$this->init();
		return $this;
	}

	/**
	 * Создает документ по шаблону
	 * @param int $template_id
	 * @param mixed $parent_document_id
	 * @return static
	 */
	public function createByTemplate(int $template_id, ?int $parent_document_id = null)
	{
		$this->init();
		$this->setParentDocumentId($parent_document_id);

		/** Вызов setTemplateId должен быть последним, так как он вызывает fillDto, чтобы избежать дублирования вызова fillDto */
		$this->setTemplateId($template_id);

		return $this;
	}

	/**
	 * Устанавливает шаблон на базе которого будет создан документ
	 * @param mixed $template_id
	 * @throws \LogicException если шаблон документа не найден
	 * @return static
	 */
	public function setTemplateId(int $template_id)
	{
		$this->template_id = $template_id;
		$this->template = DocumentTemplate::find($template_id);

		if (!$this->template) {
			throw new \LogicException("Не удалось найти шаблон документа с идентификатором {$template_id}!");
		}

		$this->fillDto();

		return $this;
	}

	/**
	 * Устанавливает родительский документ для продолжения иерархии
	 * @param mixed $parent_document_id идентификатор родительского документа
	 * @return static
	 */
	public function setParentDocumentId(?int $parent_document_id)
	{
		$this->parent_document_id = $parent_document_id;
		return $this;
	}

	/**
	 * Возвращает установленный идентификатор шаблона
	 * @return int|null
	 */
	protected function getTemplateId(): ?int
	{
		return $this->template_id;
	}

	/**
	 * Возвращает установленный идентификатор родительского
	 * @return int|null
	 */
	protected function getParentDocumentId(): ?int
	{
		return $this->parent_document_id;
	}

	protected function getTemplate(): ?DocumentTemplate
	{
		return $this->template;
	}

	/**
	 * Инициализирует построитель документа
	 */
	abstract protected function init(): void;

	/**
	 * Заполняет DTO для создания документа
	 */
	abstract protected function fillDto(): void;

	/**
	 * Валидация документа до сохранения в БД
	 * @throws \Exception
	 * @return void
	 */
	abstract protected function validate();

	/**
	 * Сохраняет созданный документ
	 */
	abstract public function save();
}