<?php
namespace SED\Documents\Common\Services;

use App\Modules\Processes\Facades\ProcessFacade;
use Carbon\Carbon;
use App\Modules\Departments\Facades\DepartmentFacade;
use Illuminate\Database\Eloquent\Builder;
use SED\Documents\Common\Dto\CreateDocumentDto;
use SED\Documents\Common\Dto\FilterDocumentsDto;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Models\Document;
use SED\Documents\Directive\Config\DirectiveConfig;
use SED\Documents\Review\Config\ReviewConfig;

class DocumentService
{
	protected FilterService $filterService;

	public function __construct(FilterService $filterService)
	{
		$this->filterService = $filterService;
	}

	public function create(CreateDocumentDto $dto): Document
	{
		$document = new Document((array) $dto);
		$document->save();

		return $document;
	}

	public function getAll(FilterDocumentsDto $dto)
	{
		$model = Document::orderBy($dto->sort, $dto->order);

		$model = VerificationService::checkListAccess($model, $dto->user_id);

		if ($dto->filters) {
			$model = $this->filterService->filter($dto->filters, $model);
		}

		$total = $model->count();
		$model = $model->offset($dto->offset)->limit($dto->limit);
		$promos = $model->get();

		return (object) [
			'items' => $promos,
			'total' => $total,
		];
	}

	public function getNeedActions(FilterDocumentsDto $dto)
	{
		$esz_ids = ProcessFacade::getDocumentsRequiringDecide(
			'ESZ',
			$dto->user_id
		);

		$directive_ids = ProcessFacade::getDocumentsRequiringDecide(
			DirectiveConfig::getModuleName(),
			$dto->user_id
		);

		$review_ids = ProcessFacade::getDocumentsRequiringDecide(
			ReviewConfig::getModuleName(),
			$dto->user_id
		);

		$model = Document::orderBy($dto->sort, $dto->order)
			->orWhere(function (Builder $query) use ($directive_ids) {
				$query->where('type_id', DocumentType::DIRECTIVE)->whereIn('document_id', $directive_ids);
			})
			->orWhere(function (Builder $query) use ($review_ids) {
				$query->where('type_id', DocumentType::REVIEW)->whereIn('document_id', $review_ids);
			})
			->orWhere(function (Builder $query) use ($esz_ids) {
				$query->where('type_id', DocumentType::ESZ)->whereIn('document_id', $esz_ids);
			});

		if ($dto->filters) {
			$model = $this->filterService->filter($dto->filters, $model);
		}

		$total = $model->count();
		$model = $model->offset($dto->offset)->limit($dto->limit);
		$promos = $model->get();

		return (object) [
			'items' => $promos,
			'total' => $total,
		];
	}

	public function getNeedActionCount(int $user_id)
	{
		$esz_count = ProcessFacade::getDocumentsRequiringDecide(
			'ESZ',
			$user_id
		)->count();

		$directive_count = ProcessFacade::getDocumentsRequiringDecide(
			DirectiveConfig::getModuleName(),
			$user_id
		)->count();

		$review_count = ProcessFacade::getDocumentsRequiringDecide(
			ReviewConfig::getModuleName(),
			$user_id
		)->count();

		return $esz_count + $directive_count + $review_count;
	}

	public function findDocument(int $document_id, int $type_id): ?Document
	{
		return Document::where(['document_id' => $document_id, 'type_id' => $type_id])->first();
	}

	/**
	 * Ищет документ по $document_id и $type_id и обновляет его по переданным данным из $dto
	 * 
	 * @param int $document_id идентификатор конкретного документа
	 * @param int $type_id тип конкретного документа
	 * @param UpdateDocumentDto $dto объект с данными для обновления
	 * 
	 * @return Document
	 */
	public function update(int $document_id, int $type_id, UpdateDocumentDto $dto): Document
	{
		$document = $this->findDocument($document_id, $type_id);

		if (!$document) {
			throw new \Exception("Не удалось найти документ по document_id $document_id и type_id $type_id");
		}

		$document->number = $dto->number;
		$document->theme = $dto->theme;
		$document->executor_id = $dto->executor_id;
		$document->status_title = $dto->status_title;
		$document->save();

		return $document;
	}

	public function delete(int $document_id, int $type_id): void
	{
		$document = $this->findDocument($document_id, $type_id);

		if (!$document) {
			throw new \Exception("Не удалось найти документ по document_id $document_id и type_id $type_id");
		}

		$document->delete();
	}

	/**
	 * Генерирует уникальный номер документа на основе типа документа, аббревиатуры подразделения и текущего года
	 * 
	 * @param int $document_id идентификатор документа
	 * @param int $type_id идентификатор типа документа
	 * @param string $department_abbreviation аббревиатура подразделения
	 * 
	 * @return string номер документа в формате: (первые буквы типа документа)-(аббревиатура департамента)-(год)-(id документа)
	 */
	public function generateDocumentNumber(int $document_id, int $type_id, string $department_abbreviation): string
	{
		$year = Carbon::now()->year;
		$first_letters_document_type = null;

		switch ($type_id) {
			case \SED\Documents\Common\Enums\DocumentType::ESZ:
				$first_letters_document_type = 'ЭСЗ';
				break;

			case \SED\Documents\Common\Enums\DocumentType::DIRECTIVE:
				$first_letters_document_type = 'П';
				break;

			case \SED\Documents\Common\Enums\DocumentType::REVIEW:
				$first_letters_document_type = 'О';
				break;

			default:
				throw new \Exception("Не реализована обработка для типа документа $type_id");
		}

		return sprintf(
			'%s-(%s)-%d-%d',
			$first_letters_document_type,
			$department_abbreviation,
			$year,
			$document_id
		);
	}
}
