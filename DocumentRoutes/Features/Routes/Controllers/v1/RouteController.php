<?php

namespace SED\DocumentRoutes\Features\Routes\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\DocumentRoutes\Features\Routes\Requests\{
	CreateRouteRequest,
	EditRouteRequest,
	GetDeleteRouteRequest
};

use SED\DocumentRoutes\Features\Routes\Services\RouteService;

class RouteController extends BaseController
{
	protected RouteService $service;

	public function __construct(RouteService $service)
	{
		$this->service = $service;
	}

	public function create(CreateRouteRequest $request)
	{
		$route = $this->service->create($request->createDto());

		return $this->sendResponse($route);
	}

	public function edit(EditRouteRequest $request)
	{
		$route = $this->service->edit($request->createDto());

		return $this->sendResponse($route);
	}

	public function delete(GetDeleteRouteRequest $request)
	{
		$this->service->delete($request->id);

		return $this->sendResponse();
	}

	public function list()
	{
		$routes = $this->service->list();

		return $this->sendResponse($routes);
	}

	public function get(GetDeleteRouteRequest $request)
	{
		$route = $this->service->get($request->id);

		return $this->sendResponse($route);
	}

	public function getAdditionalData()
	{
		$additional_data = $this->service->getAdditionalData();

		return $this->sendResponse($additional_data);
	}

	public function deactivate(GetDeleteRouteRequest $request)
	{

		$this->service->deactivate($request->id);

		return $this->sendResponse();
	}
}
