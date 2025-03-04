<?php

namespace SED\Documents\Review\Services;

use Illuminate\Support\Collection;
use App\Modules\File\Facades\FileFacade;
use SED\Common\Exceptions\NotFoundException;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Enums\DocumentType;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Common\Exceptions\AccessDeniedException;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Review\Config\DecideProcessConfig;
use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use SED\Documents\Review\Enums\{ParticipantType, Status, FileType};
use SED\Documents\Common\Dto\{UpdateDocumentDto, CreateDocumentDto, UserItemDto};
use SED\Documents\Review\Models\{Review, Participant, ReviewFile};
use SED\Documents\Review\Dto\{CreateReviewDto, UpdateReviewDto, CreateHistoryDto, GetByIdReviewDto, PreCreateReviewDto};
use SED\Documents\Review\Transitions\{ArchiveWorkedToArchiveСancelled, PreparationToArchiveСancelled};
use SED\Documents\Common\Services\UserRoleAggregatorService;

class ReviewService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected ArchiveWorkedToArchiveСancelled $archiveWorkedToArchiveСancelled;
	protected PreparationToArchiveСancelled $preparationToArchiveCancelled;
	protected VerificationService $verificationService;

	public function __construct(
		DocumentService $documentService,
		HistoryService $historyService,
		ArchiveWorkedToArchiveСancelled $archiveWorkedToArchiveСancelled,
		PreparationToArchiveСancelled $preparationToArchiveCancelled,
		VerificationService $verificationService
	) {
		$this->documentService = $documentService;
		$this->historyService = $historyService;
		$this->archiveWorkedToArchiveСancelled = $archiveWorkedToArchiveСancelled;
		$this->preparationToArchiveCancelled = $preparationToArchiveCancelled;
		$this->verificationService = $verificationService;
	}

	public function preCreate(PreCreateReviewDto $dto): Review
	{
		$create_dto = new CreateReviewDto();
		$create_dto->content = $dto->content;
		$create_dto->portfolio = $dto->portfolio;
		$create_dto->user_id = $dto->user_id;
		$create_dto->tmp_doc_id = $dto->tmp_doc_id;
		$create_dto->theme_title = $dto->theme_title;
		$create_dto->parent_document_id = $dto->parent_document_id;

		$userRoleAggregatorService = new UserRoleAggregatorService();
		$userRoleAggregatorService->setDocumentInitiator($dto->user_id);

		if ($dto->receivers->isNotEmpty()) {
			$create_dto->receivers = $userRoleAggregatorService->extractManyUsers($dto->receivers, $dto->user_id);

			if ($create_dto->receivers->isEmpty()) {
				throw new \LogicException('Получающие ознакомление не найдены!');
			}
		}

		return $this->create($create_dto);
	}

	public function create(CreateReviewDto $dto): Review
	{
		return \DB::transaction(function () use ($dto): Review {
			$department = DepartmentFacade::getByUserId($dto->user_id);

			$review = new Review();
			$review->status_id = $this->checkDraftAndReturnStatus($dto);
			$review->type_id = DocumentType::REVIEW;
			$review->process_template_id = DecideProcessConfig::getProcessTemplateId();
			$review->department_id = $department->id;

			if (isset($dto->tmp_doc_id)) {
				$review->tmp_doc_id = $dto->tmp_doc_id;
			} else if (isset($dto->theme_title)) {
				$review->theme_title = $dto->theme_title;
			} else {
				throw new \LogicException('Тема документа не была передана!');
			}

			$review->save();

			$review->contents->content = $dto->content;
			$review->contents->portfolio = $dto->portfolio;

			$review->initiator->user_id = $dto->user_id;
			$review->initiator->can_deletable = false;

			$review->receivers()->createMany(
				$dto->receivers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::RECEIVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])->toArray()
			);

			$review->number = $this->documentService->generateDocumentNumber(
				$review->id,
				DocumentType::REVIEW,
				$department->abbreviation
			);

			$review->push();

			$document_dto = new CreateDocumentDto();
			$document_dto->document_id = $review->id;
			$document_dto->number = $review->number;
			$document_dto->type_id = $review->type_id;
			$document_dto->theme = $review->theme;
			$document_dto->initiator_id = $review->initiator->user_id;
			$document_dto->status_title = $review->status->title;
			$document_dto->status_id = $review->status->id;
			$document_dto->parent_document_id = $dto->parent_document_id;
			$document_dto->template_document = $review->templateDocument;
			$document_dto->participants = $this->getDocumentParticipants($review->id);
			$document_dto->tmp_doc_id = $review->tmp_doc_id;
			$common_document = $this->documentService->create($document_dto);

			$history = new CreateHistoryDto();
			$history->review_id = $review->id;
			$history->user_id = $review->initiator->user_id;
			$history->event = "Ознакомление создано";
			$this->historyService->create($history);

			ProcessFacade::create(
				CreateProcessDto::create(
					$review->initiator->user_id,
					$review->id,
					$review->process_template_id,
					$review->initiator->user_id
				)
			);

			$review->common_document_id = $common_document->id;
			$review->save();

			return $review->fresh();
		});
	}

	public function getById(int $id, int $user_id): GetByIdReviewDto
	{
		$review = Review::find($id);

		$document_participants = $this->getDocumentParticipants($id);

		if (!$review) {
			throw new NotFoundException("Не удалось найти ознакомление по id $id");
		}

		/* ======================= TODO: Костыль для обхода проверки прав, когда сотрудник должен видеть документы из иерархии ======================= */
		$cookieValue = request()->cookie('selected_document_ids', '[]');
		$selectedDocumentIds = json_decode($cookieValue, true);
		$isDocumentSelected = in_array($review->common_document_id, $selectedDocumentIds);

		if (!$this->verificationService->checkAccess($user_id, $review, $document_participants) && !$isDocumentSelected) {
			throw new AccessDeniedException('Доступ к документу запрещен!');
		}

		$document_rights = collect([]);

		if ((bool) $this->verificationService->getDocumentFullAccess($user_id, $review->initiator->user_id)) {
			$document_rights->push('document_full_access');
		}

		return new GetByIdReviewDto($review, $document_rights);
	}

	public function findById(int $document_id): Review
	{
		$review = Review::withOnly([])->find($document_id);

		if (!$review) {
			throw new NotFoundException("Не удалось найти ознакомление по id $document_id");
		}

		return $review;
	}

	public function update(UpdateReviewDto $dto): Review
	{
		return \DB::transaction(function () use ($dto): Review {
			$review = Review::find($dto->document_id);

			if (!$review) {
				throw new NotFoundException("Не удалось найти ознакомление по id $dto->document_id");
			}

			if (!$review->isPreparation() && !$review->isDraft()) {
				throw new \LogicException('Ознакомление невозможно редактировать на текущем статусе!');
			}


			$review->contents->content = $dto->content;
			$review->contents->portfolio = $dto->portfolio;

			if ($review->isDraft()) {
				$review->status_id = $this->checkDraftAndReturnStatus($dto);
			}

			$review->save();

			$review->receivers()->delete();

			$review->receivers()->createMany(
				$dto->receivers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::RECEIVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])->toArray()
			);

			$review->push();
			$review->refresh();

			$document_dto = new UpdateDocumentDto();
			$document_dto->theme = $review->theme;
			$document_dto->initiator_id = $review->initiator->user_id;
			$document_dto->status_title = $review->status->title;
			$document_dto->status_id = $review->status->id;
			$document_dto->participants = $this->getDocumentParticipants($review->id);
			$this->documentService->update($review->id, $review->type_id, $document_dto);

			$history = new CreateHistoryDto();
			$history->review_id = $review->id;
			$history->user_id = $review->initiator->user_id;
			$history->event = "Ознакомление обновлено";
			$this->historyService->create($history);

			return $review->fresh();
		});
	}

	public function delete(int $document_id): void
	{
		$review = Review::find($document_id);

		if (!$review) {
			throw new NotFoundException("Не удалось найти ознакомление по id $document_id");
		}

		if (!$review->isPreparation()) {
			throw new \LogicException('Нельзя удалить ознакомление, которое не находится в статусе "Подготовка"');
		}

		$active_process = ProcessFacade::getActive($review->process_template_id, $document_id);

		if ($active_process->isCreated()) {
			ProcessFacade::deleteByDocumentIdAndTemplateId($document_id, $review->process_template_id);
		}

		$review->delete();
		$this->documentService->delete($review->id, $review->type_id);
	}

	public function cancel(int $document_id): Review
	{
		$review = Review::find($document_id);

		if (!$review) {
			throw new NotFoundException("Не удалось найти ознакомление по id $document_id");
		}

		if ($review->isPreparation()) {
			$this->preparationToArchiveCancelled->handle($review);
		} else if ($review->isArchiveWorked()) {
			$this->archiveWorkedToArchiveСancelled->handle($review);
		} else {
			throw new \LogicException('Нельзя удалить ознакомление, которое находится в статусе "Ознакомление"');
		}

		return $review->fresh();
	}

	public function uploadFiles(int $document_id, Collection $data)
	{

		$review = Review::find($document_id);

		if (!$review) {
			throw new NotFoundException("Не удалось найти ознакомление по id $document_id");
		}

		(new DocumentFileService($document_id, $data))
			->setType('main', FileType::MAIN, $review->mainFiles(), $review->mainFiles)
			->uploads();

		return $review->fresh();
	}

	public function getReceivers(int $document_id): Collection
	{
		return Participant::query()
			->where('review_id', $document_id)
			->where('type_id', ParticipantType::RECEIVERS)
			->pluck('user_id');
	}

	private function getDocumentParticipants(int $document_id): array
	{
		return Participant::query()
			->where('review_id', $document_id)
			->get()
			->pluck('user_id')
			->values()
			->toArray();
	}

	public function sendToApproval(int $document_id): Review
	{
		$review = $this->findById($document_id);

		if ($review->receivers->isEmpty()) {
			throw new \LogicException('Не заполнены участники ознакомления!');
		}

		if ($review->isPreparation()) {
			$active_process = ProcessFacade::rebuild(
				CreateProcessDto::create(
					$review->initiator->user_id,
					$review->id,
					$review->process_template_id,
					$review->initiator->user_id
				)
			);

			ProcessFacade::run($active_process->process->id, $review->initiator->user_id);
		}

		return $review->fresh();
	}

	public function forceDelete(int $id)
	{
		\DB::transaction(function () use ($id) {
			$review = Review::find($id);

			if (!$review) {
				throw new NotFoundException("Не удалось найти ознакомление по id $id");
			}

			$active_process = ProcessFacade::getActive($review->process_template_id, $review->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($review->id, $review->process_template_id);
			} else if ($active_process->isCompleted()) {
				ProcessFacade::deactivateCompletedProcess($review->process_template_id, $review->id);
			}

			$review->mainFiles->each(function (ReviewFile $file) {
				FileFacade::delete($file->file_id);
			});

			$review->delete();
			$this->documentService->delete($review->id, $review->type_id);
		});
	}

	/**
	 * @param CreateReviewDto|UpdateReviewDto $dto
	 * @return int
	 */
	public function checkDraftAndReturnStatus(object $dto): int
	{
		if (!$dto instanceof CreateReviewDto && !$dto instanceof UpdateReviewDto) {
			throw new \InvalidArgumentException('DTO должен быть экземпляром CreateReviewDto или UpdateReviewDto');
		}

		$has_receivers = $dto->receivers->isNotEmpty();

		return !$has_receivers ? Status::DRAFT : Status::PREPARATION;
	}
}
