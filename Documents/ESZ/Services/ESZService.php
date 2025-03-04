<?php
namespace SED\Documents\ESZ\Services;

use SED\Documents\ESZ\Models\Esz;


use Illuminate\Support\Collection;
use SED\Documents\ESZ\Models\EszFile;
use App\Modules\File\Facades\FileFacade;
use SED\Documents\ESZ\Models\Participant;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Enums\DocumentType;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\Common\Services\DocumentService;
use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use SED\Documents\Common\Services\UserRoleAggregatorService;
use SED\Documents\ESZ\Enums\{Status, FileType, ParticipantType};
use SED\Common\Exceptions\{AccessDeniedException, NotFoundException};
use SED\Documents\ESZ\Config\{CoordinationProcessConfig, SigningProcessConfig};
use SED\Documents\Common\Dto\{CreateDocumentDto, UpdateDocumentDto, UserItemDto};
use SED\Documents\ESZ\Dto\{CreateHistoryDto, UpdateESZDto, GetByIdESZDto, CreateESZDto, PreCreateESZDto};
use SED\Documents\ESZ\Transitions\{
	PreparationToArchiveCancelled,
	FixToArchiveCancelled,
	FixSigningToArchiveCancelled,
	FixResolutionToArchiveCancelled,
	PreparationToSigning
};

class ESZService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected PreparationToArchiveCancelled $preparationToArchiveCancelled;
	protected FixToArchiveCancelled $fixToArchiveCancelled;
	protected FixSigningToArchiveCancelled $fixSigningToArchiveCancelled;
	protected FixResolutionToArchiveCancelled $fixResolutionToArchiveCancelled;
	protected PreparationToSigning $preparationToSigning;
	protected VerificationService $verificationService;

	public function __construct(
		DocumentService $documentService,
		HistoryService $historyService,
		PreparationToArchiveCancelled $preparationToArchiveCancelled,
		FixToArchiveCancelled $fixToArchiveCancelled,
		FixSigningToArchiveCancelled $fixSigningToArchiveCancelled,
		FixResolutionToArchiveCancelled $fixResolutionToArchiveCancelled,
		VerificationService $verificationService,
		PreparationToSigning $preparationToSigning
	) {
		$this->documentService = $documentService;
		$this->historyService = $historyService;

		$this->preparationToArchiveCancelled = $preparationToArchiveCancelled;
		$this->fixToArchiveCancelled = $fixToArchiveCancelled;
		$this->fixSigningToArchiveCancelled = $fixSigningToArchiveCancelled;
		$this->fixResolutionToArchiveCancelled = $fixResolutionToArchiveCancelled;
		$this->verificationService = $verificationService;
		$this->preparationToSigning = $preparationToSigning;
	}

	public function preCreate(PreCreateESZDto $dto): Esz
	{
		$create_dto = new CreateESZDto();
		$create_dto->content = $dto->content;
		$create_dto->portfolio = $dto->portfolio;
		$create_dto->user_id = $dto->user_id;
		$create_dto->tmp_doc_id = $dto->tmp_doc_id;
		$create_dto->theme_title = $dto->theme_title;
		$create_dto->parent_document_id = $dto->parent_document_id;

		$userRoleAggregatorService = new UserRoleAggregatorService();
		$userRoleAggregatorService->setDocumentInitiator($dto->user_id);

		if ($dto->signatory) {
			$create_dto->signatory = $userRoleAggregatorService->extractUser($dto->signatory, $dto->user_id);

			if (!$create_dto->signatory) {
				throw new \LogicException('Подписант не найден!');
			}
		}

		if ($dto->receivers->isNotEmpty()) {
			$create_dto->receivers = $userRoleAggregatorService->extractManyUsers($dto->receivers, $dto->user_id);

			if ($create_dto->receivers->isEmpty()) {
				throw new \LogicException('Адресаты не найдены!');
			}
		}

		$create_dto->observers = $userRoleAggregatorService->extractManyUsers($dto->observers, $dto->user_id);

		return $this->create($create_dto);
	}

	public function create(CreateESZDto $dto): Esz
	{
		return \DB::transaction(function () use ($dto): Esz {
			$department = DepartmentFacade::getByUserId($dto->user_id);

			$esz = new Esz();
			$esz->status_id = $this->checkDraftAndReturnStatus($dto);
			$esz->type_id = DocumentType::ESZ;
			$esz->process_template_id = CoordinationProcessConfig::getTemplateId();
			$esz->department_id = $department->id;

			if (isset($dto->tmp_doc_id)) {
				$esz->tmp_doc_id = $dto->tmp_doc_id;
			} else if (isset($dto->theme_title)) {
				$esz->theme_title = $dto->theme_title;
			} else {
				throw new \LogicException('Тема документа не была передана!');
			}

			$esz->save();

			$esz->contents->content = $dto->content;
			$esz->contents->portfolio = $dto->portfolio;

			$esz->initiator()->create([
				'type_id' => ParticipantType::INITIATOR,
				'user_id' => $dto->user_id,
				'can_deletable' => false,
			]);

			if ($dto->signatory) {
				$esz->signatory()->create([
					'type_id' => ParticipantType::SIGNATORY,
					'user_id' => $dto->signatory->user_id,
					'can_deletable' => $dto->signatory->can_deletable,
				]);
			}

			$esz->receivers()->createMany(
				$dto->receivers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::RECEIVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])->toArray()
			);

			$esz->observers()->createMany(
				$dto->observers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::OBSERVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])->toArray()
			);

			$esz->number = $this->documentService->generateDocumentNumber(
				$esz->id,
				DocumentType::ESZ,
				$department->abbreviation
			);

			$esz->push();

			$document_dto = new CreateDocumentDto();
			$document_dto->document_id = $esz->id;
			$document_dto->number = $esz->number;
			$document_dto->type_id = $esz->type_id;
			$document_dto->theme = $esz->theme;
			$document_dto->initiator_id = $esz->initiator->user_id;
			$document_dto->status_title = $esz->status->title;
			$document_dto->status_id = $esz->status->id;
			$document_dto->parent_document_id = $dto->parent_document_id;
			$document_dto->template_document = $esz->templateDocument;
			$document_dto->participants = $this->getDocumentParticipants($esz->id);
			$document_dto->tmp_doc_id = $esz->tmp_doc_id;

			$common_document = $this->documentService->create($document_dto);

			$history = new CreateHistoryDto();
			$history->esz_id = $esz->id;
			$history->user_id = $esz->initiator->user_id;
			$history->event = "ЭСЗ создано";
			$this->historyService->create($history);

			ProcessFacade::create(
				CreateProcessDto::create(
					$esz->initiator->user_id,
					$esz->id,
					$esz->process_template_id,
					$esz->initiator->user_id
				)
			);

			$esz->common_document_id = $common_document->id;
			$esz->save();

			return $esz->fresh();
		});
	}

	public function getById(int $id, int $user_id): GetByIdESZDto
	{
		$esz = Esz::find($id);

		$document_participants = $this->getDocumentParticipants($id);

		if (!$esz) {
			throw new NotFoundException("Не удалось найти ЭСЗ по id $id");
		}

		/* ======================= TODO: Костыль для обхода проверки прав, когда сотрудник должен видеть документы из иерархии ======================= */
		$cookieValue = request()->cookie('selected_document_ids', '[]');
		$selectedDocumentIds = json_decode($cookieValue, true);
		$isDocumentSelected = in_array($esz->common_document_id, $selectedDocumentIds);

		if (!$this->verificationService->checkAccess($user_id, $esz, $document_participants) && !$isDocumentSelected) {
			throw new AccessDeniedException('Доступ к документу запрещен!');
		}

		$document_rights = collect([]);

		if ((bool) $this->verificationService->getDocumentFullAccess($user_id, $esz->initiator->user_id)) {
			$document_rights->push('document_full_access');
		}

		return new GetByIdESZDto($esz, $document_rights);
	}

	public function findById(int $document_id): Esz
	{
		$esz = Esz::withOnly([])->find($document_id);

		if (!$esz) {
			throw new NotFoundException("Не удалось найти ЭСЗ по id $document_id");
		}

		return $esz;
	}

	public function update(UpdateESZDto $dto): Esz
	{
		return \DB::transaction(function () use ($dto): Esz {
			$esz = ESZ::find($dto->document_id);

			if (!$esz) {
				throw new NotFoundException("Не удалось найти ЭСЗ по id $dto->document_id");
			}

			if (!($esz->isDraft() || $esz->isPreparation() || $esz->isFix() || $esz->isFixSigning() || $esz->isFixResolution())) {
				throw new \LogicException('ЭСЗ невозможно редактировать на текущем статусе!');
			}

			$esz->contents->content = $dto->content;
			$esz->contents->portfolio = $dto->portfolio;

			$esz->signatory()->delete();
			$esz->receivers()->delete();
			$esz->observers()->delete();

			$esz->signatory()->create([
				'type_id' => ParticipantType::SIGNATORY,
				'user_id' => $dto->signatory->user_id,
				'can_deletable' => $dto->signatory->can_deletable,
			]);

			$esz->receivers()->createMany(
				$dto->receivers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::RECEIVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])->toArray()
			);

			$esz->observers()->createMany(
				$dto->observers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::OBSERVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])->toArray()
			);

			if ($esz->isDraft()) {
				$esz->status_id = $this->checkDraftAndReturnStatus($dto);
			}

			$esz->push();
			$esz->refresh();

			$document_dto = new UpdateDocumentDto();
			$document_dto->theme = $esz->theme;
			$document_dto->initiator_id = $esz->initiator->user_id;
			$document_dto->status_title = $esz->status->title;
			$document_dto->status_id = $esz->status->id;
			$document_dto->participants = $this->getDocumentParticipants($esz->id);

			$this->documentService->update($esz->id, $esz->type_id, $document_dto);

			$history = new CreateHistoryDto();
			$history->esz_id = $esz->id;
			$history->user_id = $esz->initiator->user_id;
			$history->event = "ЭСЗ обновлено";
			$this->historyService->create($history);

			return $esz->fresh();
		});
	}

	public function delete(int $id): void
	{
		\DB::transaction(function () use ($id): void {
			$esz = Esz::find($id);

			if (!$esz) {
				throw new NotFoundException("Не удалось найти ЭСЗ по id $id");
			}

			if (!$esz->isPreparation()) {
				throw new \LogicException('Нельзя удалить ЭСЗ, которое не находится в статусе "Подготовка"');
			}

			$active_process = ProcessFacade::getActive($esz->process_template_id, $esz->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($id, $esz->process_template_id);
			}

			$esz->delete();
			$this->documentService->delete($esz->id, $esz->type_id);
		});
	}

	public function forceDelete(int $id): void
	{
		\DB::transaction(function () use ($id): void {
			$esz = Esz::find($id);

			if (!$esz) {
				throw new NotFoundException("Не удалось найти ЭСЗ по id $id");
			}

			$active_process = ProcessFacade::getActive($esz->process_template_id, $esz->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($id, $esz->process_template_id);
			}

			$esz->mainFiles->each(function (EszFile $file) {
				FileFacade::delete($file->file_id);
			});

			$esz->additionalFiles->each(function (EszFile $file) {
				FileFacade::delete($file->file_id);
			});

			$esz->delete();
			$this->documentService->delete($esz->id, $esz->type_id);
		});
	}

	public function uploadFiles(int $document_id, Collection $data): void
	{

		$esz = Esz::find($document_id);

		if (!$esz) {
			throw new NotFoundException("Не удалось найти ЭСЗ по id $document_id");
		}

		(new DocumentFileService($document_id, $data))
			->setType('main', FileType::MAIN, $esz->mainFiles(), $esz->mainFiles)
			->setType('additional', FileType::ADDITIONAL, $esz->additionalFiles(), $esz->additionalFiles)
			->uploads();
	}

	public function sendToApproval(int $document_id): Esz
	{
		$esz = $this->findById($document_id);

		if ($esz->isFixSigning() || $esz->isFixResolution()) {
			ProcessFacade::rebuild(
				CreateProcessDto::create(
					$esz->initiator->user_id,
					$esz->id,
					$esz->process_template_id,
					$esz->initiator->user_id
				)
			);
		}

		return $esz->fresh();
	}

	public function cancellation(int $document_id, int $user_id): Esz
	{
		return \DB::transaction(function () use ($document_id, $user_id) {
			$esz = Esz::find($document_id);

			if (!$esz) {
				throw new NotFoundException("Не удалось найти ЭСЗ по id $document_id");
			}

			if ($esz->isPreparation()) {
				$this->preparationToArchiveCancelled->execute($esz);
			} else if ($esz->isFix()) {
				$this->fixToArchiveCancelled->execute($esz);
			} else if ($esz->isFixSigning()) {
				$this->fixSigningToArchiveCancelled->execute($esz);
			} else if ($esz->isFixResolution()) {
				$this->fixResolutionToArchiveCancelled->execute($esz);
			} else {
				throw new \LogicException('ЭСЗ не может быть аннулирован на текущем статусе!');
			}

			$active_process = ProcessFacade::getActive($esz->process_template_id, $esz->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($esz->id, $esz->process_template_id);
			}

			$esz = $esz->fresh();

			return $esz;
		});
	}

	public function sendToSignatory(int $document_id, int $user_id): Esz
	{
		return \DB::transaction(function () use ($document_id, $user_id) {
			$esz = $this->findById($document_id);

			if (!$esz) {
				throw new NotFoundException("Не удалось найти ЭСЗ по id $document_id");
			}

			if (!$esz->isPreparation()) {
				throw new \LogicException('ЭСЗ не может быть отправлен на подписание на текущем статусе!');
			}

			$active_process = ProcessFacade::getActive($esz->process_template_id, $esz->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($esz->id, $esz->process_template_id);
			} else if ($active_process->isCompleted()) {
				ProcessFacade::deactivateCompletedProcess($esz->process_template_id, $esz->id);
			}

			$esz->process_template_id = SigningProcessConfig::getTemplateId();
			$esz->save();

			ProcessFacade::create(
				CreateProcessDto::create(
					$esz->initiator->user_id,
					$esz->id,
					SigningProcessConfig::getTemplateId(),
					$esz->initiator->user_id
				)
			);
			return $esz->fresh();

		});
	}

	private function getDocumentParticipants(int $document_id): array
	{
		return Participant::query()
			->where('esz_id', $document_id)
			->get()
			->pluck('user_id')
			->values()
			->toArray();
	}

	/**
	 * @param CreateESZDto|UpdateESZDto $dto
	 * @return int Возвращает id статуса документа
	 */
	private function checkDraftAndReturnStatus(object $dto): int
	{
		if (!$dto instanceof CreateESZDto && !$dto instanceof UpdateESZDto) {
			throw new \InvalidArgumentException('Dto должен быть экземпляром CreateESZDto или UpdateESZDto');
		}

		$has_signer = !empty($dto->signatory);
		$has_receivers = $dto->receivers->isNotEmpty();

		return (!$has_signer && !$has_receivers) ? Status::DRAFT : Status::PREPARATION;
	}
}