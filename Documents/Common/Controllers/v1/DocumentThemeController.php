<?php
namespace SED\Documents\Common\Controllers\v1;

use SED\Common\Controllers\BaseController;
use SED\Documents\Common\Services\DocumentThemeService;

class DocumentThemeController extends BaseController
{
	protected DocumentThemeService $service;

	public function __construct(DocumentThemeService $service)
	{
		$this->service = $service;
	}

	public function getAll()
	{
		$themes = $this->service->getAll();
		return $this->sendResponse($themes);
	}
}