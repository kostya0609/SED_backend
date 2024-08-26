<?php
namespace SED\Documents\Review\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Requests\GetByIdRequest;
use SED\Common\Controllers\BaseController;
use SED\Documents\Review\Requests\CreateReviewRequest;
use SED\Documents\Review\Requests\UpdateReviewRequest;
use SED\Documents\Review\Services\ReviewService;

class ReviewController extends BaseController
{
    protected ReviewService $service;

    public function __construct(ReviewService $service)
    {
        $this->service = $service;
    }

    public function create(CreateReviewRequest $request)
    {
        $review = $this->service->create($request->createDto());
        return $this->sendResponse($review);
    }

    public function getById(GetByIdRequest $request)
    {
        $review = $this->service->getById($request->document_id, $request->user_id);
        return $this->sendResponse($review);
    }

    public function update(UpdateReviewRequest $request)
    {
        $review = $this->service->update($request->createDto());
        return $this->sendResponse($review);
    }

    public function delete(GetByIdRequest $request)
    {
        $this->service->delete($request->document_id);
        return $this->sendResponse();
    }

    public function cancel(GetByIdRequest $request)
    {
        $review = $this->service->cancel($request->document_id);
        return $this->sendResponse($review);
    }

    public function uploadFiles(Request $request)
    {
        $result = $this->service->uploadFiles($request->document_id, collect($request->data));

        return $this->sendResponse($result);
    }

    public function sendToApproval(GetByIdRequest $request)
	{
		$this->service->sendToApproval($request->document_id);
		return $this->sendResponse();
	}
}
