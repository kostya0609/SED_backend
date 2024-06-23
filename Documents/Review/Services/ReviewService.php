<?php
namespace SED\Documents\Review\Services;

use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use App\Modules\Processes\Facades\ProcessFacade;
use Illuminate\Support\Collection;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Dto\CreateDocumentDto;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Review\Dto\CreateUpdateReviewDto;
use SED\Documents\Review\Dto\CreateHistoryDto;
use SED\Documents\Review\Enums\ParticipantType;
use SED\Documents\Review\Enums\Status;
use SED\Documents\Review\Enums\FileType;
use SED\Documents\Review\Models\Contents;
use SED\Documents\Review\Models\Review;
use SED\Documents\Review\Config\DecideProcessConfig;
use SED\Documents\Review\Models\Participant;

use SED\Documents\Review\Transitions\ArchiveWorkedToArchiveСancelled;

class ReviewService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected ArchiveWorkedToArchiveСancelled $archiveWorkedToArchiveСancelled;

	public function __construct(DocumentService $documentService, HistoryService $historyService, ArchiveWorkedToArchiveСancelled $archiveWorkedToArchiveСancelled)
	{
		$this->documentService = $documentService;
		$this->historyService = $historyService;
		$this->archiveWorkedToArchiveСancelled = $archiveWorkedToArchiveСancelled;
	}

	public function create(CreateUpdateReviewDto $dto): Review
	{
		return \DB::transaction(function () use ($dto): Review {
			$department = DepartmentFacade::getByUserId($dto->responsible_id);

			$review = new Review();
			$review->status_id = Status::PREPARATION;
			$review->type_id = DocumentType::REVIEW;
			$review->theme_id = $dto->theme_id;
			$review->process_template_id = DecideProcessConfig::getProcessTemplateId();
			$review->department_id = $department->id;
			$review->save();

			$contents = new Contents(['content' => $dto->content, 'portfolio' => $dto->portfolio]);
			$review->contents()->save($contents);

			$review->responsible()->create([
				'type_id' => ParticipantType::RESPONSIBLE,
				'user_id' => $dto->responsible_id,
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
			$review->save();
			$review = $review->fresh();

			$document_dto = new CreateDocumentDto();
			$document_dto->document_id = $review->id;
			$document_dto->number = $review->number;
			$document_dto->type_id = $review->type_id;
			$document_dto->theme = $review->theme->title;
			$document_dto->executor_id = $review->responsible->user_id;
			$document_dto->status_title = $review->status->title;
			$this->documentService->create($document_dto);

			$history = new CreateHistoryDto();
			$history->review_id = $review->id;
			$history->user_id = $review->responsible->user_id;
			$history->event = "Ознакомление создано";
			$this->historyService->create($history);

			ProcessFacade::create(
				CreateProcessDto::create(
					$review->responsible->user_id,
					$review->id,
					$review->process_template_id,
					$review->responsible->user_id
				)
			);

			return $review->fresh();
		});
	}

	public function getById(int $id, int $user_id): Review
	{
		$review = Review::find($id);

		if (!$review) {
			throw new \Exception("Не удалось найти ознакомление по id $id");
		}

		if (!VerificationService::checkAccess($user_id, $review)) {
			throw new \Exception("Отсутствуют права на получение данных по ознакомление с $id");
		}

		$review->full_access = (bool) VerificationService::checkFullAccess($user_id, $review->responsible->user_id);

		return $review;
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
			$department = DepartmentFacade::getByUserId($dto->responsible_id);

			if (!$review) {
				throw new \Exception("Не удалось найти ознакомление по id $dto->document_id");
			}

			$review->contents->content = $dto->content;
			$review->contents->portfolio = $dto->portfolio;
			$review->theme_id = $dto->theme_id;

			$review->responsible->user_id = $dto->responsible_id;

			$review->number = $this->documentService->generateDocumentNumber(
				$review->id,
				DocumentType::REVIEW,
				$department->abbreviation
			);

			$review->save();

			$review->theme()->associate($dto->theme_id);

			$review->receivers()->delete();

			$review->receivers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::RECEIVERS, 'user_id' => $user_id],
					$dto->receivers
				)
			);

			$review->push();

			$document_dto = new UpdateDocumentDto();
			$document_dto->number = $review->number;
			$document_dto->theme = $review->theme->title;
			$document_dto->executor_id = $review->responsible->user_id;
			$document_dto->status_title = $review->status->title;
			$this->documentService->update($review->id, $review->type_id, $document_dto);

			$history = new CreateHistoryDto();
			$history->review_id = $review->id;
			$history->user_id = $review->responsible->user_id;
			$history->event = "Ознакомление обновлено";
			$this->historyService->create($history);

			$review->fresh();

			$review->full_access = (bool) VerificationService::checkFullAccess($dto->user_id, $review->responsible->user_id);

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

		ProcessFacade::deleteByDocumentIdAndTemplateId($document_id, $review->process_template_id);
		$this->documentService->delete($review->id, $review->type_id);
		$review->delete();
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
}
