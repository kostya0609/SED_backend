<?php
namespace SED\DocumentRoutes\Features\DocumentTemplates\Services;

use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

/**
 * Не самый хороший вариант построения дерева шаблонов документа с удалением $template->children и заново его присвоение с новыми данными.
 * В laravel иначе сделать тяжело, поэтому пока такое решение. Заменил удаление через unset на метод setRelation у моделей, но тоже так себе решение.
 */
class TreeCreatorService
{
	public function createTree(int $route_id): Collection
	{
		$templates = DocumentTemplate::query()
			->where('route_id', $route_id)
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

		foreach ($childrens as $child) {

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
}