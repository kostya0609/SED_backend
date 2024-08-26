<?php
namespace SED\Documents\Common\Services\BasedCreation;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\DocumentTemplate;
use SED\Documents\Common\Models\Document;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Models\DocumentHierarchy;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Common\Services\BasedCreation\Creators\CreatorBasedOnEszTemplate;

class BasedCreationService
{
	private const CREATORS = [
		DocumentType::ESZ => CreatorBasedOnEszTemplate::class,
	];

	private DocumentService $document_service;

	public function __construct(DocumentService $document_service)
	{
		$this->document_service = $document_service;
	}

	public function createFrom(int $based_document_id, array $template_ids): Collection
	{
		$base_document = $this->document_service->findById($based_document_id);
		$documents = collect([]);

		if (!$base_document) {
			throw new \Exception("Не удалось найти базовый документ по id $based_document_id");
		}

		$templates = DocumentTemplate::findMany($template_ids);

		foreach ($templates as $template) {
			$document = $this->createByTemplate($base_document, $template);
			$documents->push($document);

			$hierarchy_document = new DocumentHierarchy([
				'document_id' => $document->id,
				'parent_document_id' => $base_document->id,
				'is_start' => $template->is_start,
				'concrete_document_id' => $document->document_id,
				'number' => $document->number,
			]);

			$hierarchy_document->save();
		}

		return $documents;
	}

	private function createByTemplate(Document $base_document, DocumentTemplate $template): Document
	{
		$creator_class = self::CREATORS[$template->type_id] ?? null;

		if (!$creator_class) {
			throw new \Exception("Не найден класс создателя для документа с type_id {$template->type_id}");
		}

		$creator = \App::make($creator_class);

		return $creator->create($base_document, $template);
	}
}