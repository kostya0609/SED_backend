<?php
namespace SED\Report\Documents\ESZ;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SED\Report\Interfaces\DocumentFacade;

class ESZReportFacade implements DocumentFacade
{
	private ESZExcelFactory $factory;

	public function __construct(ESZExcelFactory $factory)
	{
		$this->factory = $factory;
	}

	public function createExcelWorkSheet(Worksheet $sheet): Worksheet
	{
		return $this->factory->create($sheet);
	}
}