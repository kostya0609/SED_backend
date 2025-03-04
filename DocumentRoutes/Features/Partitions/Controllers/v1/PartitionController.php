<?php

namespace SED\DocumentRoutes\Features\Partitions\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\DocumentRoutes\Features\Partitions\Requests\{
	CreatePartitionRequest,
	GetDeletePartitionRequest,
	EditPartitionRequest
};

use SED\DocumentRoutes\Features\Partitions\Services\PartitionService;

class PartitionController extends BaseController
{
	protected PartitionService $service;

	public function __construct(PartitionService $service)
	{
		$this->service = $service;
	}

	public function create(CreatePartitionRequest $request)
	{

		$partition = $this->service->create($request->createDto());

		return $this->sendResponse($partition);
	}

	public function edit(EditPartitionRequest $request)
	{
		$partition = $this->service->edit($request->createDto());
		return $this->sendResponse($partition);
	}

	public function delete(GetDeletePartitionRequest $request)
	{
		$this->service->delete($request->id);
		return $this->sendResponse();
	}

	public function getTree()
	{
		$tree = $this->service->getTree();
		return $this->sendResponse($tree);
	}

	public function getTreeForSelectTemplate(Request $request)
	{
		$validated = (object) $request->validate(
			[
				'user_id' => 'required|integer',
			],
			[
				'user_id.required' => 'Поле user_id обязательно для заполнения',
				'user_id.integer' => 'Поле user_id должно быть числом',
			]
		);

		$tree = $this->service->getTreeForSelectTemplate($validated->user_id);
		return $this->sendResponse($tree);
	}

	public function get(GetDeletePartitionRequest $request)
	{
		$partition = $this->service->get($request->id);
		return $this->sendResponse($partition);
	}

	public function getBreadcrumbs(Request $request) {
		$breadcrumbs = $this->service->getBreadcrumbs($request->id);
        return $this->sendResponse($breadcrumbs);
	}
}
