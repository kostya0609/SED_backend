<?php
namespace SED\Report\Documents\Review;

use SED\Report\Interfaces\DocumentFacade;
use \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReviewReportFacade implements DocumentFacade
{
	private ReviewExcelFactory $factory;
	public function __construct(ReviewExcelFactory $factory)
	{
		$this->factory = $factory;
	}

	public function createExcelWorkSheet(Worksheet $sheet): Worksheet
	{
		return $this->factory->create($sheet);
	}
}