<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\DocumentRoutes\Features\DocumentTemplates\Requests\{
	CreateDocTmpRequest,
	EditDocTmpRequest,
	GetDeleteDeactivateDocTmpRequest
};

use SED\DocumentRoutes\Features\DocumentTemplates\Services\DocumentTemplateService;

class DocumentTemplatesController extends BaseController
{
	protected DocumentTemplateService $service;

	public function __construct(DocumentTemplateService $service)
	{
		$this->service = $service;
	}

	public function create(CreateDocTmpRequest $request)
	{

		$doc_tmp = $this->service->create($request->createDto());

		return $this->sendResponse($doc_tmp);
	}

	public function edit(EditDocTmpRequest $request)
	{

		$doc_tmp = $this->service->edit($request->createDto());

		return $this->sendResponse($doc_tmp);
	}

	public function delete(GetDeleteDeactivateDocTmpRequest $request)
	{

		$this->service->delete($request->id);

		return $this->sendResponse();
	}


	public function deactivate(GetDeleteDeactivateDocTmpRequest $request)
	{

		$this->service->deactivate($request->id);

		return $this->sendResponse();
	}

	public function list(Request $request)
	{
		$request->validate(
			[
				'route_id' => 'required|integer',
			],
			[
				'route_id.required' => 'ID маршрута не было передано!',
				'route_id.integer' => 'ID маршрута должен быть целым числом!',
			]
		);

		$doc_tmps = $this->service->list($request->route_id);

		return $this->sendResponse($doc_tmps);
	}

	public function get(GetDeleteDeactivateDocTmpRequest $request)
	{
		$doc_tmp = $this->service->get($request->id);

		return $this->sendResponse($doc_tmp);
	}
}
