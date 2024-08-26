<?php
namespace SED\Documents\Review\Services;

use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use App\Modules\Processes\Facades\ProcessFacade;
use Illuminate\Support\Collection;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Dto\{UpdateDocumentDto, CreateDocumentDto};
use SED\Documents\Review\Dto\{CreateUpdateReviewDto, CreateHistoryDto, GetByIdReviewDto};
use SED\Documents\Review\Enums\{ParticipantType, Status, FileType};
use SED\Documents\Review\Models\{Contents, Review, Participant};
use SED\Documents\Review\Config\DecideProcessConfig;
use SED\Documents\Review\Transitions\ArchiveWorkedToArchiveСancelled;

class ReviewService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected ArchiveWorkedToArchiveСancelled $archiveWorkedToArchiveСancelled;
	protected VerificationService $verificationService;

	public function __construct(
		DocumentService $documentService,
		HistoryService $historyService,
		ArchiveWorkedToArchiveСancelled $archiveWorkedToArchiveСancelled,
		VerificationService $verificationService
	) {
		$this->documentService = $documentService;
		$this->historyService = $historyService;
		$this->archiveWorkedToArchiveСancelled = $archiveWorkedToArchiveСancelled;
		$this->verificationService = $verificationService;
	}

	public function create(CreateUpdateReviewDto $dto): Review
	{
		return \DB::transaction(function () use ($dto): Review {
			$department = DepartmentFacade::getByUserId($dto->user_id);

			$review = new Review();
			$review->status_id = Status::PREPARATION;
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

			$contents = new Contents(['content' => $dto->content, 'portfolio' => $dto->portfolio]);
			$review->contents()->save($contents);

			$review->initiator()->create([
				'type_id' => ParticipantType::INITIATOR,
				'user_id' => $dto->user_id,
			]);
			$review->receivers()->createMany(
				array_map(fn($user_id) => [
					'type_id' => ParticipantType::RECEIVERS,
					'user_id' => $user_id,
				], $dto->receivers)
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
			$document_dto->participants = $this->getDocumentParticipants($review->id);
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
			throw new \Exception("Не удалось найти ознакомление по id $id");
		}

		if (!$this->verificationService->checkAccess($user_id, $review, $document_participants)) {
			throw new \Exception("Отсутствуют права на получение данных по ознакомление с $id");
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
			throw new \Exception("Не удалось найти ознакомление по id $document_id");
		}

		return $review;
	}

	public function update(CreateUpdateReviewDto $dto): Review
	{
		return \DB::transaction(function () use ($dto): Review {
			$review = Review::find($dto->document_id);

			if (!$review) {
				throw new \Exception("Не удалось найти ознакомление по id $dto->document_id");
			}

			$review->contents->content = $dto->content;
			$review->contents->portfolio = $dto->portfolio;

			$review->save();

			$review->receivers()->delete();

			$review->receivers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::RECEIVERS, 'user_id' => $user_id],
					$dto->receivers
				)
			);

			$review->push();

			$document_dto = new UpdateDocumentDto();
			$document_dto->theme = $review->theme;
			$document_dto->initiator_id = $review->initiator->user_id;
			$document_dto->status_title = $review->status->title;
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
			throw new \Exception("Не удалось найти ознакомление по id $document_id");
		}

		if (!$review->isPreparation()) {
			throw new \Exception('Нельзя удалить ознакомление, которое не находится в статусе "Подготовка"');
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
			throw new \Exception("Не удалось найти ознакомление по id $document_id");
		}

		if (!$review->isArchiveWorked()) {
			throw new \Exception('Нельзя удалить ознакомление, которое не находится в статусе "Архив отработано"');
		}

		$this->archiveWorkedToArchiveСancelled->handle($review);

		return $review->fresh();
	}

	public function uploadFiles(int $document_id, Collection $data)
	{

		$review = Review::find($document_id);

		if (!$review) {
			throw new \Exception("Не удалось найти ознакомление по id $document_id");
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

	public function sendToApproval(int $document_id): void
	{
		$review = $this->findById($document_id);

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
	}
}
