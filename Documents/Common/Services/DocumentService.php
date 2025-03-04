<?php
namespace SED\Documents\Common\Services;

use App\Modules\CountControl\Facades\NeedActionFacade;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use SED\Common\Config\SEDConfig;
use SED\Common\Exceptions\NotFoundException;
use SED\Documents\Common\Models\Document;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Models\DocumentType as DocumentTypeModel;
use SED\Documents\Common\Models\DocumentHierarchy;
use SED\Documents\Common\Models\DocumentHistory;
use SED\Documents\ESZ\Config\ESZConfig;
use SED\Documents\Review\Config\ReviewConfig;
use SED\Documents\Common\Dto\CreateDocumentDto;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Dto\FilterDocumentsDto;
use SED\Documents\Directive\Config\DirectiveConfig;

use App\Modules\BsiTable\FilterFacade;
use SED\Common\Models\User;

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

		$parent_document = $dto->parent_document_id ? DocumentHierarchy::firstWhere('document_id', $dto->parent_document_id) : null;

		$hierarchy_document = new DocumentHierarchy([
			'document_id' => $document->id,
			'parent_document_id' => $dto->parent_document_id,
			'is_start' => $dto->template_document ? $dto->template_document->is_start : false,
			'concrete_document_id' => $document->document_id,
			'start_document_id' => $parent_document ? $parent_document->start_document_id : $document->id,
			'number' => $document->number,
		]);

		$hierarchy_document->save();

		return $document;
	}

	/**
	 * Возвращает общий список документов
	 * 
	 * @deprecated Использовался для старой версии грида
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
	 * Возвращает общий список документов
	 * 
	 * @param int $user_id
	 * @return \App\Modules\BsiTable\Filter\BsiTablePaginator
	 */
	public function getAllV2(int $user_id)
	{
		$model = Document::query()->with(['initiator']);

		$model = $this->verificationService->checkListAccess($model, $user_id);

		$custom_sort_fields = [
			'initiator_id' => User::select('LAST_NAME')->whereColumn('b_user.ID', 'l_sed_documents.initiator_id'),
			'type_id' => DocumentTypeModel::select('title')->whereColumn('l_sed_document_types.id', 'l_sed_documents.type_id'),
		];

		$search_fields = [
			'number' => '%like%',
			'status_title' => '%like%',
			'initiator_id' => 'user-like',
			'theme' => '%like%',
		];

		return FilterFacade::sort($custom_sort_fields)
			->filter()
			->search($search_fields, function (Builder $builder, $search) {
				$builder->orWhereIn('type_id', function ($builder) use ($search) {
					$builder
						->select(['id'])
						->from('l_sed_document_types')
						->where('title', 'LIKE', "%{$search}%");
				});
			})
			->getAll($model);
	}

	public function getAllCount(int $user_id): int
	{
		$model = Document::where('initiator_id', $user_id);

		return $model->count();
	}

	/**
	 * Возвращает общий список документов, требующих реакции от пользователя
	 * 
	 * @deprecated Использовался для старой версии грида
	 */
	public function getNeedActions(FilterDocumentsDto $dto): object
	{
		$esz_ids = NeedActionFacade::getNeedAction(ESZConfig::getModuleName(), $dto->user_id)->getDocuments();
		$directive_ids = NeedActionFacade::getNeedAction(DirectiveConfig::getModuleName(), $dto->user_id)->getDocuments();
		$review_ids = NeedActionFacade::getNeedAction(ReviewConfig::getModuleName(), $dto->user_id)->getDocuments();

		$model = Document::query()
			->where(function (Builder $query) use ($dto, $esz_ids, $directive_ids, $review_ids) {
				$query->orderBy($dto->sort, $dto->order)
					->orWhere(function (Builder $query) use ($esz_ids) {
						$query->where('type_id', DocumentType::ESZ)->whereIn('document_id', $esz_ids);
					})
					->orWhere(function (Builder $query) use ($directive_ids) {
						$query->where('type_id', DocumentType::DIRECTIVE)->whereIn('document_id', $directive_ids);
					})
					->orWhere(function (Builder $query) use ($review_ids) {
						$query->where('type_id', DocumentType::REVIEW)->whereIn('document_id', $review_ids);
					})
					->orWhere(function (Builder $query) use ($dto) {
						$query->where('type_id', DocumentType::ESZ)
							->whereIn('status_id', [\SED\Documents\ESZ\Enums\Status::FIX, \SED\Documents\ESZ\Enums\Status::FIX_RESOLUTION])
							->where('initiator_id', $dto->user_id);
					});
			});


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
	 * 
	 * @param int $user_id
	 * @return \App\Modules\BsiTable\Filter\BsiTablePaginator
	 */
	public function getNeedActionsV2(int $user_id)
	{
		$custom_sort_fields = [
			'initiator_id' => User::select('LAST_NAME')->whereColumn('b_user.ID', 'l_sed_documents.initiator_id'),
			'type_id' => DocumentTypeModel::select('title')->whereColumn('l_sed_document_types.id', 'l_sed_documents.type_id'),
		];

		$search_fields = [
			'number' => '%like%',
			'status_title' => '%like%',
			'initiator_id' => 'user-like',
			'theme' => '%like%',
		];

		$esz_ids = NeedActionFacade::getNeedAction(ESZConfig::getModuleName(), $user_id)->getDocuments();
		$directive_ids = NeedActionFacade::getNeedAction(DirectiveConfig::getModuleName(), $user_id)->getDocuments();
		$review_ids = NeedActionFacade::getNeedAction(ReviewConfig::getModuleName(), $user_id)->getDocuments();

		$model = Document::query()->with(['initiator'])
			->where(function (Builder $query) use ($user_id, $esz_ids, $directive_ids, $review_ids) {
				$query->orWhere(function (Builder $query) use ($esz_ids) {
					$query->where('type_id', DocumentType::ESZ)->whereIn('document_id', $esz_ids);
				})
					->orWhere(function (Builder $query) use ($directive_ids) {
						$query->where('type_id', DocumentType::DIRECTIVE)->whereIn('document_id', $directive_ids);
					})
					->orWhere(function (Builder $query) use ($review_ids) {
						$query->where('type_id', DocumentType::REVIEW)->whereIn('document_id', $review_ids);
					})
					->orWhere(function (Builder $query) use ($user_id) {
						$query->where('type_id', DocumentType::ESZ)
							->whereIn('status_id', [\SED\Documents\ESZ\Enums\Status::FIX, \SED\Documents\ESZ\Enums\Status::FIX_RESOLUTION])
							->where('initiator_id', $user_id);
					});
			});

		return FilterFacade::sort($custom_sort_fields)
			->filter()
			->search($search_fields, function (Builder $builder, $search) {
				$builder->orWhereIn('type_id', function ($builder) use ($search) {
					$builder
						->select(['id'])
						->from('l_sed_document_types')
						->where('title', 'LIKE', "%{$search}%");
				});
			})
			->getAll($model);
	}

	/**
	 * Возвращает общий список документов, требующих реакции от заместителя
	 * 
	 * @deprecated Использовался для старой версии грида
	 */
	public function getNeedActionSubusers(FilterDocumentsDto $dto): object
	{
		$model = Document::query()
			->where(function (Builder $query) use ($dto) {
				$query->orWhere($this->getDocumentQueryBuilder(ESZConfig::getModuleName(), DocumentType::ESZ, $dto->user_id))
					->orWhere($this->getDocumentQueryBuilder(DirectiveConfig::getModuleName(), DocumentType::DIRECTIVE, $dto->user_id))
					->orWhere($this->getDocumentQueryBuilder(ReviewConfig::getModuleName(), DocumentType::REVIEW, $dto->user_id))
					->orderBy($dto->sort, $dto->order);
			});

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
	 * Возвращает общий список документов, требующих реакции от заместителя
	 * 
	 * @param int $user_id
	 * @return \App\Modules\BsiTable\Filter\BsiTablePaginator
	 */
	public function getNeedActionSubusersV2(int $user_id)
	{
		$search_fields = [
			'id' => '=',
			'number' => '%like%',
			'status_title' => '%like%',
			'initiator_id' => 'user-like',
			'theme' => '%like%',
		];

		$custom_sort_fields = [
			'initiator_id' => User::select('LAST_NAME')->whereColumn('b_user.ID', 'l_sed_documents.initiator_id'),
			'type_id' => DocumentTypeModel::select('title')->whereColumn('l_sed_document_types.id', 'l_sed_documents.type_id'),
		];

		$model = Document::query()->with(['initiator'])
			->where(function (Builder $query) use ($user_id) {
				$query->orWhere($this->getDocumentQueryBuilder(ESZConfig::getModuleName(), DocumentType::ESZ, $user_id))
					->orWhere($this->getDocumentQueryBuilder(DirectiveConfig::getModuleName(), DocumentType::DIRECTIVE, $user_id))
					->orWhere($this->getDocumentQueryBuilder(ReviewConfig::getModuleName(), DocumentType::REVIEW, $user_id));
			});

		$model = $this->verificationService->checkListAccess($model, $user_id);

		return FilterFacade::sort($custom_sort_fields)
			->filter()
			->search($search_fields, function (Builder $builder, $search) {
				$builder->orWhereIn('type_id', function ($builder) use ($search) {
					$builder
						->select(['id'])
						->from('l_sed_document_types')
						->where('title', 'LIKE', "%{$search}%");
				});
			})
			->getAll($model);
	}

	/**
	 * Возвращает общее кол-во документов, требующих реакции от пользователя
	 */
	public function getNeedActionCount(int $user_id): int
	{
		$count = Document::where('type_id', DocumentType::ESZ)
			->whereIn('status_id', [\SED\Documents\ESZ\Enums\Status::FIX, \SED\Documents\ESZ\Enums\Status::FIX_RESOLUTION])
			->where('initiator_id', $user_id)
			->count();

		return NeedActionFacade::getCount(SEDConfig::getModuleName(), $user_id) + $count;
	}

	/**
	 * Возвращает общее кол-во документов, требующих реакции от заместителя
	 */
	public function getNeedActionSubuserCount(int $user_id): int
	{
		return Document::query()
			->orWhere($this->getDocumentQueryBuilder(ESZConfig::getModuleName(), DocumentType::ESZ, $user_id))
			->orWhere($this->getDocumentQueryBuilder(DirectiveConfig::getModuleName(), DocumentType::DIRECTIVE, $user_id))
			->orWhere($this->getDocumentQueryBuilder(ReviewConfig::getModuleName(), DocumentType::REVIEW, $user_id))
			->count();
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
			throw new NotFoundException("Не удалось найти документ по document_id $document_id и type_id $type_id");
		}

		$document->theme = $dto->theme;
		$document->initiator_id = $dto->initiator_id;
		$document->status_title = $dto->status_title;
		$document->status_id = $dto->status_id;
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
	 * @throws NotFoundException
	 */
	public function delete(int $document_id, int $type_id): void
	{
		$document = $this->findDocument($document_id, $type_id);

		if (!$document) {
			throw new NotFoundException("Не удалось найти документ по document_id $document_id и type_id $type_id");
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
				throw new \LogicException("Не реализована обработка для типа документа $type_id");
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

	public function getAllStatuses(): Collection
	{
		return \DB::query()
			->selectRaw('statuses.title')
			->fromSub(function ($query) {
				$query->select('title')
					->from('l_esz_statuses')
					->union(\DB::table('l_directive_statuses')->select('title'))
					->union(\DB::table('l_review_statuses')->select('title'));
			}, 'statuses')
			->groupBy('statuses.title')
			->orderBy('title')
			->pluck('title')
			->values();
	}

	private function getSubusersQuery(string $module_name, int $subuser_id): \Illuminate\Database\Query\Builder
	{
		$subQuery = \DB::table('l_accesses_sub_users')
			->select('replace_user_id')
			->where('sub_user_id', $subuser_id)
			->where('module', SEDConfig::getModuleName());

		return \DB::table('l_processes')
			->join('l_processes_tmp', 'l_processes_tmp.id', '=', 'l_processes.template_id')
			->join('l_processes_participants', 'l_processes_participants.process_id', '=', 'l_processes.id')
			->select('l_processes.document_id')
			->where('l_processes_tmp.module_name', $module_name)
			->where('l_processes.status_id', '>', 2)
			->where('l_processes_participants.status_id', 2)
			->whereIn('l_processes_participants.user_id', $subQuery);
	}

	private function getDocumentQueryBuilder(string $module_name, int $document_type_id, $subuser_id): callable
	{
		return function (Builder $builder) use ($module_name, $document_type_id, $subuser_id) {
			return $builder
				->whereIn('document_id', $this->getSubusersQuery($module_name, $subuser_id))
				->where('type_id', $document_type_id);
		};
	}
}
