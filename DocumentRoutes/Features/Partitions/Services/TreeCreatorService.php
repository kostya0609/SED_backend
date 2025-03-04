<?php
namespace SED\DocumentRoutes\Features\Partitions\Services;

use App\Modules\Departments\Facades\DepartmentFacade;
use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;
use SED\DocumentRoutes\Features\Partitions\Models\PartitionTree;
use SED\DocumentRoutes\Features\Routes\Models\Route;

class TreeCreatorService
{
	private int $department_id;

	public function createTree(int $user_id): Collection
	{
		$department = DepartmentFacade::getByUserId($user_id);
		$this->department_id = $department->id;

		$tree = PartitionTree::query()
			->with(['routes'])
			->whereNull('parent_id')
			->get();

		return $this->filterPartitions($tree);
	}

	private function filterPartitions($partitions): Collection
	{
		$result = Collection::make();

		foreach ($partitions as $partition) {
			if ($partition->routes->isNotEmpty() || $partition->children->isNotEmpty()) {
				$children = $partition->children;
				$routes = $partition->routes;

				$partition->setRelation('children', $this->filterPartitions($children));
				$partition->setRelation('routes', $this->filterRoutes($routes));
				$result->push($partition);
			}
		}

		return $result->filter(function (PartitionTree $partition): bool {
			return $partition->is_active && $partition->routes->isNotEmpty() || $partition->children->isNotEmpty();
		})->values();
	}

	private function filterRoutes(Collection $routes): Collection
	{
		$filtered_routes = $routes->map(function (Route $route): Route {
			$templates = $route->documentTemplates;
			$route->setRelation('documentTemplates', $this->filterTemplates($templates));
			return $route;
		});

		$filtered_routes = $filtered_routes->filter(function (Route $route): bool {
			return $route->is_active && $route->documentTemplates->isNotEmpty() && $route->departments->contains('department_id', $this->department_id);
		});

		return $filtered_routes->values();
	}

	private function filterTemplates(Collection $templates): Collection
	{
		return $templates
			->filter(function (DocumentTemplate $template) {
				return !(\DB::table('l_route_tmp_doc_relations')->where('child_template_id', $template->id)->exists());
			})
			->map(function (DocumentTemplate $template) {
				$template->setRelation('children', Collection::make());
				return $template;
			})
			->filter(function (DocumentTemplate $template): bool {
				return $template->is_active && $template->is_start;
			})->values();
	}
}