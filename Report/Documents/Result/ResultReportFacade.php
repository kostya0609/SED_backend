<?php
namespace SED\Report\Documents\Result;

use SED\Report\Interfaces\DocumentFacade;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultReportFacade implements DocumentFacade
{
	private ResultExcelFactory $factory;
	public function __construct(ResultExcelFactory $factory)
	{
		$this->factory = $factory;
	}
	public function createExcelWorkSheet(Worksheet $sheet): Worksheet
	{
		return $this->factory->create($sheet);
	}
}