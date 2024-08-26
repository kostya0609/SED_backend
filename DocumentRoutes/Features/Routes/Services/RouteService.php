<?php

namespace SED\DocumentRoutes\Features\Routes\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use SED\DocumentRoutes\Features\Routes\Dto\{
	CreateRouteDto,
	EditRouteDto
};
use App\Modules\BsiTable\FilterFacade;
use App\Modules\Users\Models\User;
use SED\DocumentRoutes\Features\Routes\Models\{
	Route,
	Direction,
	Group
};
use SED\DocumentRoutes\VerificationService;

class RouteService
{
	protected VerificationService $verificationService;

	public function __construct(VerificationService $verificationService)
	{
		$this->verificationService = $verificationService;
	}

	public function create(CreateRouteDto $dto): Route
	{
		return \DB::transaction(function () use ($dto): Route {
			$route = new Route();
			$route->title = $dto->title;
			$route->description = $dto->description;
			$route->creator_id = $dto->user_id;
			$route->last_editor_id = $dto->user_id;
			$route->direction_id = $dto->direction_id;
			$route->group_id = $dto->group_id;
			$route->is_active = $dto->is_active;
			$route->partition_id = $dto->partition_id;
			$route->save();

			$route->departments()->createMany(
				array_map(fn($dep_id) => ['department_id' => $dep_id], $dto->departments)
			);

			return $route->fresh();
		});
	}

	public function edit(EditRouteDto $dto): Route
	{
		return \DB::transaction(function () use ($dto): Route {
			$route = Route::find($dto->id);

			if (!$route) {
				throw new \Exception("Не удалось найти маршрут по id $dto->id");
			}

			$route->title = $dto->title;
			$route->description = $dto->description;
			$route->last_editor_id = $dto->user_id;
			$route->direction_id = $dto->direction_id;
			$route->group_id = $dto->group_id;
			$route->is_active = $dto->is_active;
			$route->partition_id = $dto->partition_id;
			$route->save();

			$route->departments()->delete();

			$route->departments()->createMany(
				array_map(fn($dep_id) => ['department_id' => $dep_id], $dto->departments)
			);

			return $route->fresh();
		});
	}

	public function delete(int $id): void
	{
		$route = Route::find($id);

		if (!$route) {
			throw new \Exception("Не удалось найти маршрут по id $id");
		}

		$route->delete();
	}

	public function list()
	{
		$search_fields = [
			'id' => '%like%',
			'title' => '%like%',
			'creator_id' => 'user-like',
			'last_editor_id' => 'user-like',
		];

		$custom_sort_fields = [
			'creator' => User::select('LAST_NAME')->whereColumn('b_user.ID', 'l_route_routes.creator_id'),
			'last_editor' => User::select('LAST_NAME')->whereColumn('b_user.ID', 'l_route_routes.last_editor_id'),
			'group' => Group::select('title')->whereColumn('l_route_groups.id', 'l_route_routes.group_id'),
			'direction' => Direction::select('title')->whereColumn('l_route_directions.id', 'l_route_routes.direction_id'),
		];

		return FilterFacade::sort($custom_sort_fields)
			->filter()
			->search($search_fields, function (Builder $query, string $search) {
				$query->orWhereIn('group_id', function ($builder) use ($search) {
					$builder
						->select(['id'])
						->from('l_route_groups')
						->where('title', 'LIKE', "%{$search}%");
				});
				$query->orWhereIn('direction_id', function ($builder) use ($search) {
					$builder
						->select(['id'])
						->from('l_route_directions')
						->where('title', 'LIKE', "%{$search}%");
				});
			})
			->getAll(Route::query());
	}

	public function get(int $id): Route
	{
		$route = Route::find($id);

		if (!$route) {
			throw new \Exception("Не удалось найти маршрут по id $id");
		}

		return $route;
	}

	public function getAdditionalData(): Collection
	{
		return collect([
			'directions' => Direction::all(),
			'groups' => Group::all(),
		]);

	}

	public function deactivate(int $id): Route
	{
		$route = Route::find($id);

		if (!$route) {
			throw new \Exception("Не удалось найти маршрут по id $id");
		}

		$route->is_active = false;

		$route->save();

		return $route;
	}

}
