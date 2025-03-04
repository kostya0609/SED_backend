<?php
namespace SED\Documents\Directive\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Requests\GetByIdRequest;
use SED\Common\Controllers\BaseController;
use SED\Documents\Directive\Requests\PreCreateDirectiveRequest;
use SED\Documents\Directive\Requests\UpdateDirectiveRequest;
use SED\Documents\Directive\Services\DirectiveService;

class DirectiveController extends BaseController
{
	protected DirectiveService $service;

	public function __construct(DirectiveService $service)
	{
		$this->service = $service;
	}

	public function preCreate(PreCreateDirectiveRequest $request)
	{
		$directive = $this->service->preCreate($request->createDto());
		return $this->sendResponse($directive);
	}

	public function getById(GetByIdRequest $request)
	{
		$directive = $this->service->getById($request->document_id, $request->user_id);
		return $this->sendResponse($directive);
	}

	public function update(UpdateDirectiveRequest $request)
	{
		$directive = $this->service->update($request->createDto());
		return $this->sendResponse($directive);
	}

	public function delete(GetByIdRequest $request)
	{
		$this->service->delete($request->document_id);
		return $this->sendResponse();
	}

	public function cancel(GetByIdRequest $request)
	{
		$directive = $this->service->cancel($request->document_id, $request->user_id);
		return $this->sendResponse($directive);
	}

	public function uploadFiles(Request $request)
	{
		$this->service->uploadFiles($request->document_id, collect($request->data));

		return $this->sendResponse();
	}

	public function sendToApproval(GetByIdRequest $request)
	{

		$directive = $this->service->sendToApproval($request->document_id, $request->user_id);
		return $this->sendResponse($directive);
	}
}
