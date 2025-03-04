<?php

namespace SED\BasedCreation;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\DocumentTemplate;
use SED\Documents\Common\Models\Document;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Services\DocumentService;

use SED\BasedCreation\Creators\CreatorBasedOnEszTemplate;
use SED\BasedCreation\Creators\CreatorBasedOnReviewTemplate;
use SED\BasedCreation\Creators\CreatorBasedOnDirectiveTemplate;

class BasedCreationService
{
	private const CREATORS = [
		DocumentType::ESZ => CreatorBasedOnEszTemplate::class,
		DocumentType::DIRECTIVE => CreatorBasedOnDirectiveTemplate::class,
		DocumentType::REVIEW => CreatorBasedOnReviewTemplate::class,
	];

	private DocumentService $document_service;

	public function __construct(DocumentService $document_service)
	{
		$this->document_service = $document_service;
	}

	/**
	 * @param int $based_document_id идентификатор общего документа
	 * @param array $template_ids
	 * @throws \Exception
	 * @return \Illuminate\Support\Collection
	 */
	public function createFrom(?int $based_document_id = null, array $template_ids, ?int $initiator_id = null): Collection
	{		
		if(!$based_document_id && !$initiator_id){
			throw new \Exception("Необходимо передать или id базового документа или id инициатора документа!");
		};

		$documents = collect([]);

		$base_document = null;
		if ($based_document_id) {
			$base_document = $this->document_service->findById($based_document_id);

			if (!$base_document) {
				throw new \Exception("Не удалось найти базовый документ по id $based_document_id");
			}
		}

		$templates = DocumentTemplate::findMany($template_ids);

		foreach ($templates as $template) {
			$document = $this->createDocumentByTemplate($base_document, $template, $initiator_id);
			$documents->push($document);
		}

		return $documents;
	}

	private function createDocumentByTemplate(?Document $base_document = null, DocumentTemplate $template, ?int $initiator_id = null): Document
	{
		$creator_class = self::CREATORS[$template->type_id] ?? null;

		if (!$creator_class) {
			throw new \LogicException("Не найден класс создателя для документа с type_id {$template->type_id}");
		}

		/**
		 * @var BasedCreationInterface
		 */
		$creator = \App::make($creator_class);

		return $creator->create($base_document, $template, $initiator_id);
	}
}
