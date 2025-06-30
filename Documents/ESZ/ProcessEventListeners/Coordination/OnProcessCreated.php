<?php
namespace SED\Documents\ESZ\ProcessEventListeners\Coordination;

use App\Modules\ApprovalRoutes\ApprovalRouteFacade;
use App\Modules\Processes\Events\ProcessCreated;
use SED\Documents\ESZ\Services\ESZService;

class OnProcessCreated
{
	public function handle(ProcessCreated $event, ESZService $service)
	{
		$process = $event->getProcess();
		$esz = $service->findById($process->document_id);

		if ($esz->templateDocument && $esz->templateDocument->approvalRoutes->count() === 1) {
			$route = $esz->templateDocument->approvalRoutes->first()->approvalRoute;
			
			if ($route) {
				ApprovalRouteFacade::apply($process->id, $route->id);
			}
		}
	}
}
