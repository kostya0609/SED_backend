<?php
namespace SED\Documents\Directive\Services;

use App\Modules\Departments\Facades\DepartmentFacade;
use App\Modules\DocumentsHierarchy\DocumentsHierarchyFacade;
use App\Modules\DocumentsHierarchy\Dto\CreateDocumentHierarchyDto;
use App\Modules\File\Facades\FileFacade;
use App\Modules\Processes\Dto\Publics\CreateProcessDto;
use App\Modules\Processes\Facades\ProcessFacade;
use Illuminate\Support\Collection;
use SED\Common\Exceptions\NotFoundException;
use SED\Common\Services\DocumentFileService;
use SED\Documents\Common\Dto\CreateDocumentDto;
use SED\Documents\Common\Dto\UpdateDocumentDto;
use SED\Documents\Common\Dto\UserItemDto;
use SED\Documents\Common\Enums\DocumentType;
use SED\Common\Exceptions\AccessDeniedException;
use SED\Documents\Common\Services\DocumentService;
use SED\Documents\Common\Services\UserRoleAggregatorService;
use SED\Documents\Directive\Config\DirectiveConfig;
use SED\Documents\Directive\Config\ExecutionProcessConfig;
use SED\Documents\Directive\Dto\CreateDirectiveDto;
use SED\Documents\Directive\Dto\CreateHistoryDto;
use SED\Documents\Directive\Dto\GetByIdDirectiveDto;
use SED\Documents\Directive\Dto\PreCreateDirectiveDto;
use SED\Documents\Directive\Dto\UpdateDirectiveDto;
use SED\Documents\Directive\Enums\FileType;
use SED\Documents\Directive\Enums\ParticipantType;
use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Contents;
use SED\Documents\Directive\Models\Directive;
use SED\Documents\Directive\Models\DirectiveFile;
use SED\Documents\Directive\Models\Participant;
use SED\Documents\Directive\Transitions\PreparationToArchiveCancelled;

class DirectiveService
{
	protected DocumentService $documentService;
	protected HistoryService $historyService;
	protected PreparationToArchiveCancelled $preparationToArchiveCancelled;
	protected VerificationService $verificationService;

	public function __construct(
		DocumentService $documentService,
		HistoryService $historyService,
		PreparationToArchiveCancelled $preparationToArchiveCancelled,
		VerificationService $verificationService
	) {
		$this->documentService = $documentService;
		$this->historyService = $historyService;
		$this->preparationToArchiveCancelled = $preparationToArchiveCancelled;
		$this->verificationService = $verificationService;
	}

	public function preCreate(PreCreateDirectiveDto $dto): Directive
	{
        \Log::debug('preCreate', ['root_tmp_id' => $dto->root_tmp_id]);

        $create_dto = new CreateDirectiveDto();
		$create_dto->executed_at = $dto->executed_at;
		$create_dto->content = $dto->content;
		$create_dto->portfolio = $dto->portfolio;
		$create_dto->creator_id = $dto->creator_id;
		$create_dto->tmp_doc_id = $dto->tmp_doc_id;
		$create_dto->theme_title = $dto->theme_title;
		$create_dto->parent_document_id = $dto->parent_document_id;
		$create_dto->document_hierarchy_id = $dto->document_hierarchy_id;

        $create_dto->root_tmp_id = $dto->root_tmp_id;



        $userRoleAggregatorService = new UserRoleAggregatorService();
		$userRoleAggregatorService->setDocumentInitiator($dto->creator_id);

		if ($dto->author) {
			$create_dto->author = $userRoleAggregatorService->extractUser($dto->author, $dto->creator_id);
		}

		if ($dto->executors->isNotEmpty()) {
			$create_dto->executors = $userRoleAggregatorService->extractManyUsers($dto->executors, $dto->creator_id);
		}

		$create_dto->controllers = $userRoleAggregatorService->extractManyUsers($dto->controllers, $dto->creator_id);
		$create_dto->observers = $userRoleAggregatorService->extractManyUsers($dto->observers, $dto->creator_id);

		return $this->create($create_dto);
	}

	public function create(CreateDirectiveDto $dto): Directive
	{
		return \DB::transaction(function () use ($dto): Directive {
            \Log::debug('create', ['root_tmp_id' => $dto->root_tmp_id]);

            $department = DepartmentFacade::getByUserId($dto->creator_id);
			$directive = new Directive();
			$directive->status_id = $this->checkDraftAndReturnStatus($dto);
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
				'can_deletable' => false,
			]);

			if ($dto->author) {
				$directive->author()->create([
					'type_id' => ParticipantType::AUTHOR,
					'user_id' => $dto->author->user_id,
					'can_deletable' => $dto->author->can_deletable,
				]);
			}

			$directive->executors()->createMany(
				$dto->executors->map(
					fn(UserItemDto $participant) => [
						'type_id' => ParticipantType::EXECUTORS,
						'user_id' => $participant->user_id,
						'can_deletable' => $participant->can_deletable,
					]
				)
			);

			$directive->controllers()->createMany(
				$dto->controllers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::CONTROLLERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])
			);

			$directive->observers()->createMany(
				$dto->observers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::OBSERVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])
			);

			$directive->number = $this->documentService->generateDocumentNumber(
				$directive->id,
				DocumentType::DIRECTIVE,
				$department->abbreviation
			);
			$directive->save();
			$directive = $directive->fresh();

			$create_document_hierarchy_dto = new CreateDocumentHierarchyDto();
			$create_document_hierarchy_dto->document_id = $directive->id;
			$create_document_hierarchy_dto->module_name = DirectiveConfig::getModuleName();
			$create_document_hierarchy_dto->title = $directive->number;
			$create_document_hierarchy_dto->status_title = $directive->status->title;
			$create_document_hierarchy_dto->link = '/sed/documents/directive/detail/:id';
			$create_document_hierarchy_dto->data = [
				'theme' => $directive->theme,
			];
			$hierarchy_document = DocumentsHierarchyFacade::create($create_document_hierarchy_dto, $dto->document_hierarchy_id);

			DocumentsHierarchyFacade::addParticipants($hierarchy_document->id, $this->getDocumentParticipants($directive->id));

			$document_dto = new CreateDocumentDto();
			$document_dto->document_id = $directive->id;
			$document_dto->number = $directive->number;
			$document_dto->type_id = $directive->type_id;
			$document_dto->theme = $directive->theme;
			$document_dto->initiator_id = $directive->creator->user_id;
			$document_dto->status_title = $directive->status->title;
			$document_dto->status_id = $directive->status->id;
			$document_dto->parent_document_id = $dto->parent_document_id;
			$document_dto->template_document = $directive->templateDocument;
			$document_dto->participants = $this->getDocumentParticipants($directive->id);
			$document_dto->tmp_doc_id = $directive->tmp_doc_id;
			$document_dto->content = $dto->content;
			$document_dto->document_hierarchy_id = $hierarchy_document->id;

            //для автоматизации
            $document_dto->root_tmp_id = $dto->root_tmp_id;;


			$common_document = $this->documentService->create($document_dto);

			$history = new CreateHistoryDto();
			$history->directive_id = $directive->id;
			$history->user_id = $directive->creator->user_id;
			$history->event = "Поручение создано";
			$this->historyService->create($history);

			if ($dto->author) {
				ProcessFacade::create(
					CreateProcessDto::create(
						$directive->author->user_id,
						$directive->id,
						$directive->process_template_id,
						$directive->creator->user_id
					)
				);
			}

			$directive->document_hierarchy_id = $hierarchy_document->id;
			$directive->common_document_id = $common_document->id;
			$directive->save();


			return $directive->fresh();
		});
	}

	public function getById(int $id, int $user_id): GetByIdDirectiveDto
	{
		$directive = Directive::find($id);

		if (!$directive) {
			throw new NotFoundException("Не удалось найти поручение по id $id");
		}

		/* ======================= TODO: Костыль для обхода проверки прав, когда сотрудник должен видеть документы из иерархии ======================= */
		$cookieValue = request()->cookie('selected_document_ids', '[]');
		$selectedDocumentIds = json_decode($cookieValue, true);
		$isDocumentSelected = in_array($directive->common_document_id, $selectedDocumentIds);

		if (!$this->verificationService->checkAccess($user_id, $directive, $this->getDocumentParticipants($id)) && !$isDocumentSelected) {
			throw new AccessDeniedException('Доступ к документу запрещен!');
		}

		$document_rights = collect([]);

		if ((bool) $this->verificationService->getDocumentFullAccess($user_id, $directive->creator->user_id, $directive->author ?? null)) {
			$document_rights->push('document_full_access');
		}

		return new GetByIdDirectiveDto($directive, $document_rights);
	}

	public function findById(int $document_id): Directive
	{
		$directive = Directive::withOnly([])->find($document_id);

		if (!$directive) {
			throw new NotFoundException("Не удалось найти поручение по id $document_id");
		}

		return $directive;
	}

	public function update(UpdateDirectiveDto $dto): Directive
	{
		return \DB::transaction(function () use ($dto): Directive {
			$directive = Directive::find($dto->document_id);

			if (!$directive) {
				throw new NotFoundException("Не удалось найти поручение по id $dto->document_id");
			}

			if (!$directive->isPreparation() && !$directive->isDraft()) {
				throw new \LogicException('Поручение возможно изменить только в статусе "На подготовке"');
			}

			$directive->executed_at = $dto->executed_at;
			$directive->contents->content = $dto->content;
			$directive->contents->portfolio = $dto->portfolio;

			if ($directive->isDraft()) {
				$directive->status_id = $this->checkDraftAndReturnStatus($dto);
			}

			$directive->save();

			$directive->author()->delete();
			$directive->executors()->delete();
			$directive->controllers()->delete();
			$directive->observers()->delete();

			$directive->author()->create([
				'type_id' => ParticipantType::AUTHOR,
				'user_id' => $dto->author->user_id,
				'can_deletable' => $dto->author->can_deletable,
			]);

			$directive->executors()->createMany(
				$dto->executors->map(
					fn(UserItemDto $participant) => [
						'type_id' => ParticipantType::EXECUTORS,
						'user_id' => $participant->user_id,
						'can_deletable' => $participant->can_deletable,
					]
				)
			);

			$directive->controllers()->createMany(
				$dto->controllers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::CONTROLLERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])
			);

			$directive->observers()->createMany(
				$dto->observers->map(fn(UserItemDto $participant) => [
					'type_id' => ParticipantType::OBSERVERS,
					'user_id' => $participant->user_id,
					'can_deletable' => $participant->can_deletable,
				])
			);

			$directive->push();
			$directive->refresh();

			$document_dto = new UpdateDocumentDto();
			$document_dto->theme = $directive->theme;
			$document_dto->initiator_id = $directive->creator->user_id;
			$document_dto->status_title = $directive->status->title;
			$document_dto->status_id = $directive->status->id;
			$document_dto->participants = $this->getDocumentParticipants($directive->id);
			$document_dto->content = $dto->content;
			$this->documentService->update($directive->id, $directive->type_id, $document_dto);

			$history = new CreateHistoryDto();
			$history->directive_id = $directive->id;
			$history->user_id = $directive->creator->user_id;
			$history->event = "Поручение обновлено";
			$this->historyService->create($history);

			DocumentsHierarchyFacade::updateStatus($directive->document_hierarchy_id, $directive->status->title);
			DocumentsHierarchyFacade::syncParticipants($directive->document_hierarchy_id, $this->getDocumentParticipants($directive->id));

			return $directive->fresh();
		});
	}

	public function delete(int $id): void
	{
		\DB::transaction(function () use ($id): void {
			$directive = Directive::find($id);

			if (!$directive) {
				throw new NotFoundException("Не удалось найти поручение по id $id");
			}

			if (!$directive->isPreparation()) {
				throw new \LogicException('Нельзя удалить поручение, которое не находится в статусе "Подготовка"');
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
			throw new NotFoundException("Не удалось найти поручение по id $document_id");
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

	public function cancel(int $document_id): Directive
	{
		$directive = Directive::find($document_id);

		if (!$directive) {
			throw new NotFoundException("Не удалось найти поручение по id $document_id");
		}

		if ($directive->isPreparation()) {
			$this->preparationToArchiveCancelled->handle($directive);
		} else {
			throw new \LogicException('Нельзя отменить поручение, которое не находится в статусе "Подготовка"');
		}


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

	public function sendToApproval(int $document_id, int $user_id): Directive
	{
		$directive = $this->findById($document_id);

		if (!$directive->isPreparation()) {
			throw new \LogicException('Нельзя отправить поручение на согласование, которое не находится в статусе "Подготовка"!');
		}

		if (!$directive->author) {
			throw new \LogicException('Автор поручения не заполнен!');
		}

		if ($directive->executors->isEmpty()) {
			throw new \LogicException('Не заполнены исполнители поручения!');
		}

		$active_process = ProcessFacade::getActive($directive->process_template_id, $directive->id);

		if ($active_process->isNotCreated()) {
			$active_process = ProcessFacade::create(
				CreateProcessDto::create(
					$directive->author->user_id,
					$directive->id,
					$directive->process_template_id,
					$directive->creator->user_id
				)
			);
		} else if ($active_process->isCreated()) {
			$active_process = ProcessFacade::rebuild(
				CreateProcessDto::create(
					$directive->author->user_id,
					$directive->id,
					$directive->process_template_id,
					$directive->creator->user_id
				)
			);
		} else if ($active_process->isCompleted()) {
			throw new \LogicException('Процесс уже завершен!');
		}

		ProcessFacade::run($active_process->process->id, $user_id);

		return $directive->fresh();
	}

	public function forceDelete(int $id)
	{
		throw new \LogicException('Функционал принудительного удаления временно запрещен!');
		\DB::transaction(function () use ($id): void {
			$directive = Directive::find($id);

			if (!$directive) {
				throw new NotFoundException("Не удалось найти поручение по id $id");
			}

			$active_process = ProcessFacade::getActive($directive->process_template_id, $directive->id);

			if ($active_process->isCreated()) {
				ProcessFacade::deleteByDocumentIdAndTemplateId($id, $directive->process_template_id);
			}

			$directive->mainFiles->each(function (DirectiveFile $file) {
				FileFacade::delete($file->file_id);
			});

			DocumentsHierarchyFacade::deleteSimple($directive->document_hierarchy_id);

			$directive->delete();
			$this->documentService->delete($directive->id, $directive->type_id);
		});
	}

	/**
	 * @param CreateDirectiveDto|UpdateDirectiveDto $dto
	 * @return int
	 */
	private function checkDraftAndReturnStatus(object $dto): int
	{
		if (!$dto instanceof CreateDirectiveDto && !$dto instanceof UpdateDirectiveDto) {
			throw new \InvalidArgumentException('DTO должен быть экземпляром CreateDirectiveDto или UpdateDirectiveDto');
		}

		$has_author = !empty($dto->author);
		$has_executors = $dto->executors->isNotEmpty();

		return (!$has_author || !$has_executors) ? Status::DRAFT : Status::PREPARATION;
	}
}
