<?php
namespace SED\Documents\ESZ\Services;

use SED\Documents\ESZ\Models\Esz;
use SED\Documents\ESZ\Models\Participant;
use Illuminate\Support\Collection;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Enums\DocumentType;
use App\Modules\Processes\Facades\ProcessFacade;
use SED\Documents\Common\Services\DocumentService;
use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use SED\Documents\ESZ\Config\{CoordinationProcessConfig};
use SED\Documents\ESZ\Enums\{Status, FileType, ParticipantType};
use SED\Documents\ESZ\Dto\{CreateHistoryDto, CreateUpdateESZDto, GetByIdESZDto};
use SED\Documents\Common\Dto\{CreateDocumentDto, UpdateDocumentDto};
use SED\Documents\ESZ\Transitions\{
	PreparationToArchiveCancelled,
	FixToArchiveCancelled,
	FixSigningToArchiveCancelled,
	FixResolutionToArchiveCancelled
};

class ESZService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected PreparationToArchiveCancelled $preparationToArchiveCancelled;
	protected FixToArchiveCancelled $fixToArchiveCancelled;
	protected FixSigningToArchiveCancelled $fixSigningToArchiveCancelled;
	protected FixResolutionToArchiveCancelled $fixResolutionToArchiveCancelled;
	protected VerificationService $verificationService;

	public function __construct(
		DocumentService $documentService,
		HistoryService $historyService,
		PreparationToArchiveCancelled $preparationToArchiveCancelled,
		FixToArchiveCancelled $fixToArchiveCancelled,
		FixSigningToArchiveCancelled $fixSigningToArchiveCancelled,
		FixResolutionToArchiveCancelled $fixResolutionToArchiveCancelled,
		VerificationService $verificationService
	) {
		$this->documentService = $documentService;
		$this->historyService = $historyService;

		$this->preparationToArchiveCancelled = $preparationToArchiveCancelled;
		$this->fixToArchiveCancelled = $fixToArchiveCancelled;
		$this->fixSigningToArchiveCancelled = $fixSigningToArchiveCancelled;
		$this->fixResolutionToArchiveCancelled = $fixResolutionToArchiveCancelled;
		$this->verificationService = $verificationService;

	}

	public function create(CreateUpdateESZDto $dto): Esz
	{
		return \DB::transaction(function () use ($dto): Esz {
			$department = DepartmentFacade::getByUserId($dto->user_id);

			$esz = new Esz();
			$esz->status_id = Status::PREPARATION;
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

			$esz->initiator->user_id = $dto->user_id;
			$esz->signatory->user_id = $dto->signatory_id;

			$esz->receivers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::RECEIVERS, 'user_id' => $user_id],
					$dto->receivers
				)
			);

			$esz->observers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::OBSERVERS, 'user_id' => $user_id],
					$dto->observers
				)
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
			$document_dto->participants = $this->getDocumentParticipants($esz->id);

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
			throw new \Exception("Не удалось найти ЭСЗ по id $id");
		}

		if (!$this->verificationService->checkAccess($user_id, $esz, $document_participants)) {
			throw new \Exception("Отсутствуют права на получение данных по ЭСЗ с $id");
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
			throw new \Exception("Не удалось найти ЭСЗ по id $document_id");
		}

		return $esz;
	}

	public function update(CreateUpdateESZDto $dto): Esz
	{
		return \DB::transaction(function () use ($dto): Esz {
			$esz = ESZ::find($dto->document_id);

			if (!$esz) {
				throw new \LogicException("Не удалось найти ЭСЗ по id $dto->document_id");
			}


			if (!in_array(true, [$esz->isPreparation(), $esz->isFix(), $esz->isFixSigning(), $esz->isFixResolution()])) {
				throw new \LogicException('ЭСЗ невозможно редактировать на текущем статусе!');
			}

			$esz->contents->content = $dto->content;
			$esz->contents->portfolio = $dto->portfolio;

			$esz->signatory->user_id = $dto->signatory_id;

			$esz->receivers()->delete();
			$esz->observers()->delete();

			$esz->receivers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::RECEIVERS, 'user_id' => $user_id],
					$dto->receivers
				)
			);

			$esz->observers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::OBSERVERS, 'user_id' => $user_id],
					$dto->observers
				)
			);

			$esz->push();

			$document_dto = new UpdateDocumentDto();
			$document_dto->theme = $esz->theme;
			$document_dto->initiator_id = $esz->initiator->user_id;
			$document_dto->status_title = $esz->status->title;
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
				throw new \Exception("Не удалось найти ЭСЗ по id $id");
			}

			if (!$esz->isPreparation()) {
				throw new \Exception('Нельзя удалить ЭСЗ, которое не находится в статусе "Подготовка"');
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
				throw new \Exception("Не удалось найти ЭСЗ по id $id");
			}

			$active_process = ProcessFacade::getActive($esz->process_template_id, $esz->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($id, $esz->process_template_id);
			}

			$this->documentService->delete($esz->id, $esz->type_id);
			$esz->delete();
		});
	}

	public function uploadFiles(int $document_id, Collection $data): void
	{

		$esz = Esz::find($document_id);

		if (!$esz) {
			throw new \Exception("Не удалось найти ЭСЗ по id $document_id");
		}

		(new DocumentFileService($document_id, $data))
			->setType('main', FileType::MAIN, $esz->mainFiles(), $esz->mainFiles)
			->setType('additional', FileType::ADDITIONAL, $esz->additionalFiles(), $esz->additionalFiles)
			->uploads();
	}

	public function sendToApproval(int $document_id): void
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
	}

	public function cancellation(int $document_id, int $user_id): Esz
	{
		return \DB::transaction(function () use ($document_id, $user_id) {
			$esz = Esz::find($document_id);

			if (!$esz) {
				throw new \Exception("Не удалось найти ЭСЗ по id $document_id");
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
				throw new \Exception('ЭСЗ не может быть аннулирован на текущем статусе!');
			}

			$active_process = ProcessFacade::getActive($esz->process_template_id, $esz->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($esz->id, $esz->process_template_id);
			}

			$esz = $esz->fresh();

			return $esz;
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
}