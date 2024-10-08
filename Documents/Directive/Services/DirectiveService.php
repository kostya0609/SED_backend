<?php
namespace SED\Documents\Directive\Services;

use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use App\Modules\Processes\Facades\ProcessFacade;
use Illuminate\Support\Collection;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Dto\CreateDocumentDto;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Directive\Config\ExecutionProcessConfig;
use SED\Documents\Directive\Dto\CreateHistoryDto;
use SED\Documents\Directive\Dto\CreateUpdateDirectiveDto;
use SED\Documents\Directive\Dto\GetByIdDirectiveDto;
use SED\Documents\Directive\Enums\FileType;
use SED\Documents\Directive\Enums\ParticipantType;
use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Contents;
use SED\Documents\Directive\Models\Directive;
use SED\Documents\Directive\Models\Participant;
use SED\Documents\Directive\Transitions\ChangeRequestToInWork;

class DirectiveService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected ChangeRequestToInWork $changeRequestToInWork;
	protected VerificationService $verificationService;

	public function __construct(
		DocumentService $documentService,
		HistoryService $historyService,
		ChangeRequestToInWork $changeRequestToInWork,
		VerificationService $verificationService
	) {
		$this->documentService = $documentService;
		$this->historyService = $historyService;
		$this->changeRequestToInWork = $changeRequestToInWork;
		$this->verificationService = $verificationService;
	}

	public function create(CreateUpdateDirectiveDto $dto): Directive
	{
		return \DB::transaction(function () use ($dto): Directive {
			$department = DepartmentFacade::getByUserId($dto->creator_id);
			$directive = new Directive();
			$directive->status_id = Status::PREPARATION;
			$directive->executed_at = $dto->executed_at;
			$directive->type_id = DocumentType::DIRECTIVE;
			$directive->process_template_id = ExecutionProcessConfig::getTemplateId();
			$directive->department_id = $department->id;

			if (isset($dto->tmp_doc_id)) {
				$directive->tmp_doc_id = $dto->tmp_doc_id;
			} else if (isset($dto->theme_title)) {
				$directive->theme_title = $dto->theme_title;
			} else {
				throw new \LogicException('Тема документа не была передана!');
			}

			$directive->save();

			$contents = new Contents(['content' => $dto->content, 'portfolio' => $dto->portfolio]);
			$directive->contents()->save($contents);

			$directive->creator()->create([
				'type_id' => ParticipantType::CREATOR,
				'user_id' => $dto->creator_id,
			]);

			$directive->author()->create([
				'type_id' => ParticipantType::AUTHOR,
				'user_id' => $dto->author_id,
			]);

			$directive->executors()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::EXECUTORS, 'user_id' => $user_id],
					$dto->executors
				)
			);

			$directive->controllers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::CONTROLLERS, 'user_id' => $user_id],
					$dto->controllers
				)
			);

			$directive->observers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::OBSERVERS, 'user_id' => $user_id],
					$dto->observers
				)
			);

			$directive->number = $this->documentService->generateDocumentNumber(
				$directive->id,
				DocumentType::DIRECTIVE,
				$department->abbreviation
			);
			$directive->save();
			$directive = $directive->fresh();

			$document_dto = new CreateDocumentDto();
			$document_dto->document_id = $directive->id;
			$document_dto->number = $directive->number;
			$document_dto->type_id = $directive->type_id;
			$document_dto->theme = $directive->theme;
			$document_dto->initiator_id = $directive->creator->user_id;
			$document_dto->status_title = $directive->status->title;
			$document_dto->participants = $this->getDocumentParticipants($directive->id);
			$common_document = $this->documentService->create($document_dto);

			$history = new CreateHistoryDto();
			$history->directive_id = $directive->id;
			$history->user_id = $directive->creator->user_id;
			$history->event = "Поручение создано";
			$this->historyService->create($history);

			ProcessFacade::create(
				CreateProcessDto::create(
					$directive->creator->user_id,
					$directive->id,
					$directive->process_template_id,
					$directive->creator->user_id
				)
			);

			$directive->common_document_id = $common_document->id;
			$directive->save();

			return $directive->fresh();
		});
	}

	public function getById(int $id, int $user_id): GetByIdDirectiveDto
	{
		$directive = Directive::find($id);

		if (!$directive) {
			throw new \Exception("Не удалось найти поручение по id $id");
		}

		if (!$this->verificationService->checkAccess($user_id, $directive, $this->getDocumentParticipants($id))) {
			throw new \Exception("Нет доступа к поручению!");
		}

		$document_rights = collect([]);

		if ((bool) $this->verificationService->getDocumentFullAccess($user_id, $directive->creator->user_id, $directive->author->user_id)) {
			$document_rights->push('document_full_access');
		}

		return new GetByIdDirectiveDto($directive, $document_rights);
	}

	public function findById(int $document_id): Directive
	{
		$directive = Directive::withOnly([])->find($document_id);

		if (!$directive) {
			throw new \Exception("Не удалось найти поручение по id $document_id");
		}

		return $directive;
	}

	public function update(CreateUpdateDirectiveDto $dto): Directive
	{
		return \DB::transaction(function () use ($dto): Directive {
			$directive = Directive::find($dto->document_id);

			if (!$directive) {
				throw new \LogicException("Не удалось найти поручение по id $dto->document_id");
			}

			if (!$directive->isPreparation()) {
				throw new \LogicException('Поручение возможно изменить только в статусе "На подготовке"');
			}

			$directive->executed_at = $dto->executed_at;
			$directive->contents->content = $dto->content;
			$directive->contents->portfolio = $dto->portfolio;
			$directive->author->user_id = $dto->author_id;
			$directive->save();

			$directive->executors()->delete();
			$directive->controllers()->delete();
			$directive->observers()->delete();

			$directive->executors()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::EXECUTORS, 'user_id' => $user_id],
					$dto->executors
				)
			);

			$directive->controllers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::CONTROLLERS, 'user_id' => $user_id],
					$dto->controllers
				)
			);

			$directive->observers()->createMany(
				array_map(
					fn($user_id) => ['type_id' => ParticipantType::OBSERVERS, 'user_id' => $user_id],
					$dto->observers
				)
			);

			$directive->push();

			$document_dto = new UpdateDocumentDto();
			$document_dto->theme = $directive->theme;
			$document_dto->initiator_id = $directive->creator->user_id;
			$document_dto->status_title = $directive->status->title;
			$document_dto->participants = $this->getDocumentParticipants($directive->id);
			$this->documentService->update($directive->id, $directive->type_id, $document_dto);

			$history = new CreateHistoryDto();
			$history->directive_id = $directive->id;
			$history->user_id = $directive->creator->user_id;
			$history->event = "Поручение обновлено";
			$this->historyService->create($history);

			return $directive->fresh();
		});
	}

	public function delete(int $id): void
	{
		\DB::transaction(function () use ($id): void {
			$directive = Directive::find($id);

			if (!$directive) {
				throw new \Exception("Не удалось найти поручение по id $id");
			}

			if (!$directive->isPreparation()) {
				throw new \Exception('Нельзя удалить поручение, которое не находится в статусе "Подготовка"');
			}

			$active_process = ProcessFacade::getActive($directive->process_template_id, $directive->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($id, $directive->process_template_id);
			}

			$directive->delete();
			$this->documentService->delete($directive->id, $directive->type_id);
		});
	}

	public function uploadFiles(int $document_id, Collection $data)
	{

		$directive = Directive::find($document_id);

		if (!$directive) {
			throw new \Exception("Не удалось найти поручение по id $document_id");
		}

		(new DocumentFileService($document_id, $data))
			->setType('main', FileType::MAIN, $directive->mainFiles(), $directive->mainFiles)
			->uploads();
	}

	public function getExecutors(int $document_id): Collection
	{
		return Participant::query()
			->where('directive_id', $document_id)
			->where('type_id', ParticipantType::EXECUTORS)
			->pluck('user_id');
	}

	public function getControllers(int $document_id): Collection
	{
		return Participant::query()
			->where('directive_id', $document_id)
			->where('type_id', ParticipantType::CONTROLLERS)
			->pluck('user_id');
	}

	public function cancel(int $document_id, int $user_id): Directive
	{
		$directive = Directive::find($document_id);

		if (!$directive) {
			throw new \Exception("Не удалось найти поручение по id $document_id");
		}

		if (!$directive->isExecutionChangeRequest()) {
			throw new \Exception('Нельзя отменить поручение, которое не находится в статусе "Исполнение. Запрос на изменение"!');
		}

		$directive = $this->changeRequestToInWork->handle($directive);

		$directive = $directive->fresh();

		return $directive;
	}

	public function getDocumentParticipants(int $document_id): array
	{
		return Participant::query()
			->where('directive_id', $document_id)
			->get()
			->pluck('user_id')
			->values()
			->toArray();
	}
}