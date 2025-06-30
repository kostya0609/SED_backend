<?php

namespace App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Controllers\v1;

use App\Modules\SED\DocumentRoutes\Features\TemplatePartitions\Services\TemplatePartitionService;
use Illuminate\Http\JsonResponse;
use SED\Common\Controllers\BaseController;
use SED\DocumentRoutes\Features\TemplatePartitions\Requests\CreateTemplatePartitionRequest;
use SED\DocumentRoutes\Features\TemplatePartitions\Requests\DeleteTemplatePartitionRequest;
use SED\DocumentRoutes\Features\TemplatePartitions\Requests\EditTemplatePartitionRequest;

class TemplatePartitionController extends BaseController
{
    protected TemplatePartitionService $service;

    public function __construct(TemplatePartitionService $service)
    {
        $this->service = $service;
    }

    public function create(CreateTemplatePartitionRequest $request): JsonResponse
    {
        $tmp_partition = $this->service->create($request->createDto());

        return $this->sendResponse($tmp_partition);
    }

    public function edit(EditTemplatePartitionRequest $request): JsonResponse
    {
        $tmp_partition = $this->service->edit($request->createDto());

        return $this->sendResponse($tmp_partition);
    }

    public function delete(DeleteTemplatePartitionRequest $request): JsonResponse
    {
        $data = $this->service->delete($request->createDto());
        return $this->sendResponse($data);
    }
}
