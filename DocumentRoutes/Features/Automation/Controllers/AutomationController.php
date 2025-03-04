<?php
namespace SED\DocumentRoutes\Features\Automation\Controllers;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\DocumentRoutes\Features\Automation\Request\UpdateIsActiveRequest;
use SED\DocumentRoutes\Features\Automation\Services\AutomationService;

class AutomationController extends BaseController
{
	private AutomationService $service;

	public function __construct(AutomationService $service)
	{
		$this->service = $service;
	}

	public function getAll(Request $request)
	{
		return $this->sendResponse($this->service->getAll($request->tmp_doc_id));
	}

	public function updateIsActive(UpdateIsActiveRequest $request)
	{
		$setting = $this->service->updateIsActive($request->id, $request->tmp_doc_id, $request->is_active);
		return $this->sendResponse($setting);
	}
}