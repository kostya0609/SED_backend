<?php
namespace SED\DocumentRoutes\Features\DocumentTemplates\Services;

use App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Models\TemplatePartition;
use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

class TreeCreatorService
{


    public function createTree(int $route_id): Collection
	{
		$templates = DocumentTemplate::query()
			->where('route_id', $route_id)
			->orderBy('title', 'asc')
			->get()
			->filter(function (DocumentTemplate $template) {
				return !(\DB::table('l_route_tmp_doc_relations')->where('child_template_id', $template->id)->exists());
			})
			->values();

		/**
		 * @var DocumentTemplate $template
		 */
		foreach ($templates as $template) {
			$root_id = $template->id;
			$children = $template->children;

			$template->setBranchId($template->children->first() ? $template->children->first()->pivot->id : null);
			$template->setRootTemplateId($root_id);

			$template->setRelation('children', $this->filterChildrenTemplates($children, $root_id));
		}

		return $templates;
	}


    public function filterChildrenTemplates(Collection $childrens, int $root_id): Collection
	{
		$branch = new Collection();

		$sorted_childrens = $childrens->sortBy('title');

		foreach ($sorted_childrens as $child) {

			if ($child->pivot->root_template_id === $root_id && $child->is_active) {
				if ($child->children->isNotEmpty()) {
					$children = $child->children;
					$child->setRelation('children', $this->filterChildrenTemplates($children, $root_id));
				}

				$child->setBranchId($child->children->first() ? $child->children->first()->pivot->id : null);
				$branch->push($child);
			}
		}

		return $branch;
	}


    public function createTreeNew(int $route_id): Collection
    {
        $templates = DocumentTemplate::query()
            ->with(['children', 'childrenTemplatePartitions'])
            ->where('route_id', $route_id)
            ->orderBy('title')
            ->get()
            ->filter(function (DocumentTemplate $template) {
                return !(\DB::table('l_route_tmp_doc_relations')
                    ->where('child_template_id', $template->id)
                    ->where('child_template_type', 'template')
                    ->exists());
            })
            ->values();

        foreach ($templates as $template) {
            $root_id = $template->id;
            $children = $template->children;
            $childrenTemplatePartitions = $template->childrenTemplatePartitions;
            $allChildren = $children->concat($childrenTemplatePartitions);

            $template->setBranchId($allChildren->first() ? $allChildren->first()->pivot->id : null);
            $template->setRootTemplateId($root_id);
            unset($template->childrenTemplatePartitions);
            $template->setRelation('children', $this->filterChildrenTemplatePartitions($allChildren, $root_id));
        }

        return $templates;
    }


    public function filterChildrenTemplatePartitions(Collection $childrens, int $root_id): Collection
    {
        $branch = collect();
        if ($childrens->isEmpty()) {
            return $branch;
        }
        $sortedChildren = $childrens->sortBy('title');

        foreach ($sortedChildren as $child) {
            if (!isset($child->pivot) || $child->pivot->root_template_id !== $root_id && $child->is_active) {
                continue;
            }

            $regularChildren = $child->children ?? collect();
            $childTemplatePartitions = $child->childrenTemplatePartitions ?? collect();
            $allChildren = $regularChildren->concat($childTemplatePartitions);
            unset($child->childrenTemplatePartitions);


            if ($allChildren->isNotEmpty()) {
                $child->setRelation(
                    'children',
                    $this->filterChildrenTemplatePartitions($allChildren, $root_id)
                );
            }
            $firstChild = $child->children->first();
            $child->setBranchId($firstChild ? $firstChild->pivot->id : null);

            $branch->push($child);
        }

        return $branch;
    }


    public function filterChildrenTemplatePartitionsFoundation(Collection $children, int $root_id): Collection
    {
        if ($children->isEmpty()) {
            return collect();
        }

        return $children->sortBy('title')
            ->filter(function($child) use ($root_id)
            {
                return isset($child->pivot)
                    && $child->pivot->root_template_id === $root_id
                    && $child->is_active;
            })
            ->map(function($child) use ($root_id)
            {
                $children = $child->children ?? collect();
                $childTemplatePartitions = $child->childrenTemplatePartitions ?? collect();
                if ($child->pivot->child_template_type === 'partition'
//                    && $children->isEmpty()
                    && $childTemplatePartitions->isNotEmpty())
                {
                    $allChildren = $children->concat($childTemplatePartitions);
                    $children = $this->filterChildrenTemplatePartitionsFoundation($allChildren, $root_id);
                    $child->setRelation('children', $children);
                    $firstChild = $child->children->first();
                    $child->setBranchId($firstChild ? $firstChild->pivot->id : null);
                }

                if($child->pivot->child_template_type === 'template')
                {
                    unset($child->children);
                    $child->children = collect();
                }


                return $child;
            })
            ->values();
    }
}
