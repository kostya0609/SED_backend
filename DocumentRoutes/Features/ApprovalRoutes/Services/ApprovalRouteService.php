<?php
namespace SED\DocumentRoutes\Features\ApprovalRoutes\Services;

use App\Modules\ApprovalRoutes\ApprovalRouteResource;
use App\Modules\ApprovalRoutes\ApprovalRouteFacade;
use SED\DocumentRoutes\Features\ApprovalRoutes\Models\SEDApprovalRoute;

class ApprovalRouteService
{
	public function create(int $tmp_doc_id, string $title, int $process_template_id, bool $is_active, array $stages)
	{
		$approval_route = ApprovalRouteFacade::create($title, $process_template_id, $is_active, $stages);

		SEDApprovalRoute::create(['tmp_doc_id' => $tmp_doc_id, 'approval_route_id' => $approval_route->id]);

		return $approval_route;
	}

	public function getAll(int $tmp_doc_id, ?int $process_template_id)
	{
		$query = SEDApprovalRoute::query()
			->where('tmp_doc_id', $tmp_doc_id);

		if (isset($process_template_id)) {
			$query->whereRelation('approvalRoute', 'process_template_id', $process_template_id);
		}

		$routes = $query
			->whereHas('approvalRoute')
			->whereRelation('approvalRoute', 'is_active', true)
			->get()
			->pluck('approvalRoute');

		return ApprovalRouteResource::collection($routes);
	}

	public function update(int $approval_route_id, string $title, bool $is_active, array $stages)
	{
		return ApprovalRouteFacade::update($approval_route_id, $title, $is_active, $stages);
	}

	public function delete(int $id, int $tmp_doc_id): void
	{
		\DB::transaction(function () use ($id, $tmp_doc_id) {
			SEDApprovalRoute::query()
				->where('approval_route_id', $id)
				->where('tmp_doc_id', $tmp_doc_id)
				->delete();

			ApprovalRouteFacade::delete($id);
		});
	}
}