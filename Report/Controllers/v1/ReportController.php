<?php
namespace SED\Report\Controllers\v1;
use SED\Common\Controllers\BaseController;
use SED\Report\Services\ReportService;

class ReportController extends BaseController
{
	private ReportService $service;

	public function __construct(ReportService $service)
	{
		$this->service = $service;
	}

	public function generateExcel()
	{
		$report = $this->service->generateExcelReport();
		return $this->sendResponse($report);
	}

	public function getAll()
	{
		$reports = $this->service->getAll();
		return $this->sendResponse($reports);
	}
}