<?php
namespace SED\Documents\ESZ\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Requests\GetByIdRequest;
use SED\Common\Controllers\BaseController;
use SED\Documents\ESZ\Requests\{CreateESZRequest, UpdateESZRequest};
use SED\Documents\ESZ\Services\ESZService;

class ESZController extends BaseController
{
	protected ESZService $service;

	public function __construct(ESZService $service)
	{
		$this->service = $service;
	}

	public function create(CreateESZRequest $request)
	{
		$esz = $this->service->create($request->createDto());
		return $this->sendResponse($esz);
	}

	public function getById(GetByIdRequest $request)
	{
		$result = $this->service->getById($request->document_id, $request->user_id);
		return $this->sendResponse($result);
	}

	public function update(UpdateESZRequest $request)
	{
		$esz = $this->service->update($request->createDto());
		return $this->sendResponse($esz);
	}

	public function delete(GetByIdRequest $request)
	{
		$this->service->delete($request->document_id);
		return $this->sendResponse();
	}

	public function uploadFiles(Request $request)
	{
		$this->service->uploadFiles($request->document_id, collect($request->data));

		return $this->sendResponse();
	}

	public function sendToApproval(GetByIdRequest $request)
	{
		$this->service->sendToApproval($request->document_id);
		return $this->sendResponse();
	}

	public function cancellation(GetByIdRequest $request)
	{
		$esz = $this->service->cancellation($request->document_id, $request->user_id);
		return $this->sendResponse($esz);
	}
}
