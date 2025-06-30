<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Services;

use App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Models\TemplatePartition;
use Illuminate\Support\Collection;
use SED\Common\Exceptions\NotFoundException;
use SED\DocumentRoutes\Features\DocumentTemplates\Dto\{
	CreateDocTmpDto,
	EditDocTmpDto
};
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplateRelation;
use SED\DocumentRoutes\VerificationService;
use SED\Documents\Common\Models\Document;

class DocumentTemplateService
{
	protected VerificationService $verificationService;
	protected TreeCreatorService $treeCreatorService;

	public function __construct(VerificationService $verificationService, TreeCreatorService $treeCreatorService)
	{
		$this->verificationService = $verificationService;
		$this->treeCreatorService = $treeCreatorService;
	}

	public function create(CreateDocTmpDto $dto): DocumentTemplate
	{
		return \DB::transaction(function () use ($dto): DocumentTemplate {
			$doc_tmp = new DocumentTemplate();

			$doc_tmp->title = $dto->title;
			$doc_tmp->creator_id = $dto->user_id;
			$doc_tmp->last_editor_id = $dto->user_id;
			$doc_tmp->data = $dto->data;
			$doc_tmp->is_start = $dto->is_start;
			$doc_tmp->is_active = $dto->is_active;
			$doc_tmp->requirements = $dto->requirements;
			$doc_tmp->route_id = $dto->route_id;

			$doc_tmp->type_id = $dto->type_id;

			$doc_tmp->save();

			$parents = [];
			foreach ($dto->parents as $parent) {
				$parents[] = [
					'id' => (int) ($parent['parent_id'] . $parent['root_id']),
					'parent_template_id' => $parent['parent_id'],
					'child_template_id' => $doc_tmp->id,
					'root_template_id' => $parent['root_id'],
                    'parent_template_type' => $parent['parent_type'],
                    'child_template_type' => 'template',
				];
			}

			DocumentTemplateRelation::insert($parents);

			return $doc_tmp->fresh();
		});
	}

	public function edit(EditDocTmpDto $dto): DocumentTemplate
	{
		return \DB::transaction(function () use ($dto): DocumentTemplate {
			$doc_tmp = DocumentTemplate::find($dto->id);

			if (!$doc_tmp) {
				throw new NotFoundException("Не удалось найти шаблон документа по id $dto->id");
			}

			$doc_tmp->title = $dto->title;
			$doc_tmp->last_editor_id = $dto->user_id;
			$doc_tmp->data = $dto->data;
			$doc_tmp->is_start = $dto->is_start;
			$doc_tmp->is_active = $dto->is_active;
			$doc_tmp->route_id = $dto->route_id;

			$doc_tmp->type_id = $dto->type_id;

			$doc_tmp->save();

			DocumentTemplateRelation::where('child_template_id', $doc_tmp->id)->delete();

			$parents = [];

			/**
			 * @var array $parent
			 */
			foreach ($dto->parents as $parent) {

				if ($parent['parent_id'] == $dto->id) {
					continue;
				}

				$parents[] = [
					'id' => (int) ($parent['parent_id'] . $parent['root_id']),
					'parent_template_id' => $parent['parent_id'],
					'child_template_id' => $doc_tmp->id,
					'root_template_id' => $parent['root_id'],
                    'parent_template_type' => $parent['parent_type'],
                    'child_template_type' => 'template',
				];
			}

			DocumentTemplateRelation::insert($parents);

			return $doc_tmp->fresh();
		});
	}

	public function delete(int $id): void
	{
		$doc_tmp = DocumentTemplate::find($id);

		if (!$doc_tmp) {
			throw new NotFoundException("Не удалось найти шаблон документа по id $id");
		}

		if ($doc_tmp->check_template_usage) {
			throw new \DomainException("Нельзя удалить шаблон документа, так как он используется!");
		}

		if ($doc_tmp->children->isNotEmpty()) {
			throw new \DomainException("Нельзя удалить шаблон документа, так как он имеет дочерние шаблоны! Начните удаление с последнего элемента в ветки или проверьте другие ветки.");
		}

        $doc_tmp->children()->sync([]);
        $doc_tmp->childrenTemplatePartitions()->sync([]);

		$doc_tmp->delete();
	}

	public function deactivate(int $id): DocumentTemplate
	{
		$doc_tmp = DocumentTemplate::find($id);

		if (!$doc_tmp) {
			throw new NotFoundException("Не удалось найти шаблон документа по id $id");
		}

		$doc_tmp->is_active = false;

		$doc_tmp->save();

		return $doc_tmp;
	}

	public function list(int $route_id): Collection
	{
//		return $this->treeCreatorService->createTree($route_id);
        return $this->treeCreatorService->createTreeNew($route_id);

    }

	public function get(int $id)
	{
		$doc_tmp = DocumentTemplate::query()->with('children', 'route')->find($id);

		if (!$doc_tmp) {
			throw new NotFoundException("Не удалось найти шаблон документа по id $id");
		}

		$branches = DocumentTemplateRelation::where('child_template_id', $id)
			->get();
		unset($doc_tmp->parents);

		$doc_tmp->parents = $branches->map(function ($item)
        {

            $template = $this->getParentTemplate($item);

            $template->parent_id    = $item->parent_template_id;
            $template->parent_type  = $item->parent_template_type;
            $template->root_id      = $item->root_template_id;

			$template->setBranchId($item->id);
			$template->setRootTemplateId($item->root_template_id);


			return $template;
		});

		return $doc_tmp;
	}

    public function getParentTemplate($branch)
    {
        if($branch->parent_template_type == 'partition')
        {
            $branches = DocumentTemplateRelation::where('child_template_id', $branch->parent_template_id)->get();
            foreach ($branches as $item)
            {
                $template = $this->getParentTemplate($item);
            }
        }
        else
        {
            $template = DocumentTemplate::find($branch->parent_template_id);
        }
        return $template;
    }

	public function updateRequirements(int $id, ?string $requirements): void
	{
		$doc_tmp = DocumentTemplate::find($id);

		if (!$doc_tmp) {
			throw new NotFoundException("Не удалось найти шаблон документа по id $id");
		}

		$doc_tmp->requirements = $requirements;

		$doc_tmp->save();
	}

	public function getTreeTemplates(int $template_id, int $common_start_document_id): Collection
	{
		$template = $this->get($template_id);
		$document = Document::select('tmp_doc_id')->find($common_start_document_id);
		$root_template_id = $document ? $document->tmp_doc_id : null;

		if (!$root_template_id) {
			return new Collection();
		}

        $regularChildren = $template->children ?? collect();
        $childTemplatePartitions = $template->childrenTemplatePartitions ?? collect();
        $allChildren = $regularChildren->concat($childTemplatePartitions);
//		return $this->treeCreatorService->filterChildrenTemplates($template->children, $root_template_id);
		return $this->treeCreatorService->filterChildrenTemplatePartitionsFoundation($allChildren, $root_template_id);
	}

	public function getByStaticRole(int $static_role_id): Collection
	{
		return \DB::table('l_route_tmp_docs')
			->select('id', 'title', 'is_active')
			->whereRaw('JSON_CONTAINS(JSON_EXTRACT(data, "$**.static_role_id"), ?)', [json_encode($static_role_id)])
			->get();
	}

	public function getByDynamicRole(int $dynamic_role_id): Collection
	{
		return \DB::table('l_route_tmp_docs')
			->select('id', 'title', 'is_active')
			->whereRaw('JSON_CONTAINS(JSON_EXTRACT(data, "$**.dynamic_role_id"), ?)', [json_encode($dynamic_role_id)])
			->get();
	}
}
