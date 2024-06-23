<?php
namespace SED\Documents\Common\Controllers\v1;

use SED\Common\Controllers\BaseController;
use SED\Documents\Common\Services\DocumentTypeService;

class DocumentTypeController extends BaseController
{
    protected DocumentTypeService $documentTypeService;

    public function __construct(DocumentTypeService $documentTypeService)
    {
        $this->documentTypeService = $documentTypeService;
    }

    public function getAll()
    {
        $types = $this->documentTypeService->getAll();
        return $this->sendResponse($types);
    }
}