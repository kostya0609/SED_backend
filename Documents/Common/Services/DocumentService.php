<?php
namespace SED\Documents\Common\Services;

use App\Modules\CountControl\Facades\NeedActionFacade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use SED\Common\Config\SEDConfig;
use SED\Documents\Common\Models\Document;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Models\DocumentHistory;
use SED\Documents\ESZ\Config\ESZConfig;
use SED\Documents\Review\Config\ReviewConfig;
use SED\Documents\Common\Dto\CreateDocumentDto;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Dto\FilterDocumentsDto;
use SED\Documents\Directive\Config\DirectiveConfig;

class DocumentService
{
	protected FilterService $filterService;
	protected VerificationService $verificationService;

	public function __construct(FilterService $filterService, VerificationService $verificationService)
	{
		$this->filterService = $filterService;
		$this->verificationService = $verificationService;
	}

	/**
	 * Создает документ в общей таблицы
	 */
	public function create(CreateDocumentDto $dto): Document
	{
		$document = new Document((array) $dto);
		$document->save();

		$document->participants()->createMany(
			array_map(fn($user_id) => [
				'user_id' => $user_id,
				'document_id' => $document->id,
			], $dto->participants)
		);

		return $document;
	}

	/**
	 * Возвращает общий список документов
	 */
	public function getAll(FilterDocumentsDto $dto): object
	{
		$model = Document::orderBy($dto->sort, $dto->order);

		$model = $this->verificationService->checkListAccess($model, $dto->user_id);

		if ($dto->filters) {
			$model = $this->filterService->filter($dto->filters, $model);
		}

		$total = $model->count();
		$model = $model->offset($dto->offset)->limit($dto->limit);
		$documents = $model->get();

		return (object) [
			'items' => $documents,
			'total' => $total,
		];
	}

	/**
	 * Возвращает общий список документов, требующих реакции от пользователя
	 */
	public function getNeedActions(FilterDocumentsDto $dto): object
	{
		$esz_ids = NeedActionFacade::getNeedAction(ESZConfig::getModuleName(), $dto->user_id)->getDocuments();
		$directive_ids = NeedActionFacade::getNeedAction(DirectiveConfig::getModuleName(), $dto->user_id)->getDocuments();
		$review_ids = NeedActionFacade::getNeedAction(ReviewConfig::getModuleName(), $dto->user_id)->getDocuments();

		$model = Document::query()
			->orderBy($dto->sort, $dto->order)
			->orWhere(function (Builder $query) use ($esz_ids) {
				$query->where('type_id', DocumentType::ESZ)->whereIn('document_id', $esz_ids);
			})
			->orWhere(function (Builder $query) use ($directive_ids) {
				$query->where('type_id', DocumentType::DIRECTIVE)->whereIn('document_id', $directive_ids);
			})
			->orWhere(function (Builder $query) use ($review_ids) {
				$query->where('type_id', DocumentType::REVIEW)->whereIn('document_id', $review_ids);
			})
		;

		if ($dto->filters) {
			$model = $this->filterService->filter($dto->filters, $model);
		}

		$total = $model->count();
		$model = $model->offset($dto->offset)->limit($dto->limit);
		$documents = $model->get();

		return (object) [
			'items' => $documents,
			'total' => $total,
		];
	}

	/**
	 * Возвращает общее кол-во документов, требующих реакции от пользователя
	 */
	public function getNeedActionCount(int $user_id): int
	{
		return NeedActionFacade::getCount(SEDConfig::getModuleName(), $user_id);
	}

	/**
	 * Ищет общий документ по id конкретного документа и его типа
	 * 
	 * @param int $document_id id конкретного
	 * @param int $type_id id типа документа
	 */
	public function findDocument(int $document_id, int $type_id): ?Document
	{
		return Document::where(['document_id' => $document_id, 'type_id' => $type_id])->first();
	}

	public function findById(int $id): ?Document
	{
		return Document::find($id);
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

		$document->theme = $dto->theme;
		$document->initiator_id = $dto->initiator_id;
		$document->status_title = $dto->status_title;
		$document->save();

		if (isset($dto->participants)) {
			$document->participants()->delete();
			$document->participants()->createMany(
				array_map(fn($user_id) => [
					'user_id' => $user_id,
					'document_id' => $document->id,
				], $dto->participants)
			);
		}

		return $document;
	}

	/**
	 * Удаляет общий документ по id конкретного документа и его типа
	 * 
	 * @throws \Exception
	 */
	public function delete(int $document_id, int $type_id): void
	{
		$document = $this->findDocument($document_id, $type_id);

		if (!$document) {
			throw new \Exception("Не удалось найти документ по document_id $document_id и type_id $type_id");
		}

		$document->delete();
	}

	public function searchByNumber(string $query): Collection
	{
		$documents = Document::query()
			->select(['number as value', 'number as label'])
			->distinct()
			->where('number', 'LIKE', "%$query%")
			->limit(10)
			->get();

		return $documents;
	}

	public function searchByTheme(string $query): Collection
	{
		$documents = Document::query()
			->select(['theme as value', 'theme as label'])
			->distinct()
			->where('theme', 'LIKE', "%$query%")
			->limit(10)
			->get();

		return $documents;
	}

	/**
	 * Генерирует уникальный номер документа на основе типа документа,аббревиатуры подразделения и текущего года.
	 * Сбрасывает номер документа при переходе на следующий год. Номер документа формирует по маске с начальными нулями.
	 * Если номер документа получится больше 6 цифр, то автоматически длина маски увеличится без заполнения нулями в начале (было 999999, а след. номер станет 1000000).
	 * 
	 * @param int $document_id идентификатор документа
	 * @param int $type_id идентификатор типа документа
	 * @param string $department_abbreviation аббревиатура подразделения
	 * 
	 * @return string номер документа в формате: (первые буквы типа документа)-(аббревиатура департамента)-(год)-(номер документа)
	 */
	public function generateDocumentNumber(int $document_id, int $type_id, string $department_abbreviation): string
	{
		$year = Carbon::now()->year;
		$first_letters_document_type = null;

		switch ($type_id) {
			case DocumentType::ESZ:
				$first_letters_document_type = 'ЭСЗ';
				break;

			case DocumentType::DIRECTIVE:
				$first_letters_document_type = 'П';
				break;

			case DocumentType::REVIEW:
				$first_letters_document_type = 'О';
				break;

			default:
				throw new \Exception("Не реализована обработка для типа документа $type_id");
		}

		$number = 1;
		$last_document = DocumentHistory::query()
			->where('type_id', $type_id)
			->whereYear('created_at', $year)
			->latest('document_id')
			->first();

		if ($last_document) {
			$number = $last_document->number + 1;
		}


		$dh = new DocumentHistory([
			'document_id' => $document_id,
			'type_id' => $type_id,
			'number' => $number,
		]);

		$dh->save();

		return sprintf(
			'%s-(%s)-%d-%06d',
			$first_letters_document_type,
			$department_abbreviation,
			$year,
			$number
		);
	}
}
