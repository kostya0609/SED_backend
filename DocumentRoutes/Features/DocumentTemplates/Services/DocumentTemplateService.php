<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Services;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\DocumentTemplates\Dto\{
	CreateDocTmpDto,
	EditDocTmpDto
};
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;
use SED\DocumentRoutes\VerificationService;

class DocumentTemplateService
{
	protected VerificationService $verificationService;

	public function __construct(VerificationService $verificationService)
	{
		$this->verificationService = $verificationService;
	}

	public function create(CreateDocTmpDto $dto): DocumentTemplate
	{
		return \DB::transaction(function () use ($dto): DocumentTemplate {
			$doc_tmp = new DocumentTemplate();

			$doc_tmp->title = $dto->title;
			$doc_tmp->parent_id = $dto->parent_id;
			$doc_tmp->creator_id = $dto->user_id;
			$doc_tmp->last_editor_id = $dto->user_id;
			$doc_tmp->data = $dto->data;
			$doc_tmp->is_start = $dto->is_start;
			$doc_tmp->is_active = $dto->is_active;
			$doc_tmp->requirements = $dto->requirements;
			$doc_tmp->route_id = $dto->route_id;

			$doc_tmp->type_id = $dto->type_id;

			$doc_tmp->save();

			return $doc_tmp;
		});
	}

	public function edit(EditDocTmpDto $dto): DocumentTemplate
	{
		return \DB::transaction(function () use ($dto): DocumentTemplate {
			$doc_tmp = DocumentTemplate::find($dto->id);

			if (!$doc_tmp) {
				throw new \Exception("Не удалось найти шаблон документа по id $dto->id");
			}

			$doc_tmp->title = $dto->title;
			$doc_tmp->parent_id = $dto->parent_id;
			$doc_tmp->last_editor_id = $dto->user_id;
			$doc_tmp->data = $dto->data;
			$doc_tmp->is_start = $dto->is_start;
			$doc_tmp->is_active = $dto->is_active;
			$doc_tmp->requirements = $dto->requirements;
			$doc_tmp->route_id = $dto->route_id;

			$doc_tmp->type_id = $dto->type_id;

			$doc_tmp->save();

			return $doc_tmp;
		});
	}

	public function delete(int $id): void
	{
		$doc_tmp = DocumentTemplate::find($id);

		if (!$doc_tmp) {
			throw new \Exception("Не удалось найти шаблон документа по id $id");
		}

		$doc_tmp->delete();
	}

	public function deactivate(int $id): DocumentTemplate
	{
		$doc_tmp = DocumentTemplate::find($id);

		if (!$doc_tmp) {
			throw new \Exception("Не удалось найти шаблон документа по id $id");
		}

		$doc_tmp->is_active = false;

		$doc_tmp->save();

		return $doc_tmp;
	}

	public function list(int $route_id): Collection
	{
		$doc_tmps = DocumentTemplate::query()
			->where('route_id', $route_id)
			->whereNull('parent_id')
			->get();

		return $doc_tmps;
	}

	public function get(int $id): DocumentTemplate
	{
		$doc_tmp = DocumentTemplate::query()->with('parent')->find($id);

		if (!$doc_tmp) {
			throw new \Exception("Не удалось найти шаблон документа по id $id");
		}

		return $doc_tmp;
	}
}
