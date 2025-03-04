<?php
namespace SED\Report\Documents\Directive;

use SED\Report\Interfaces\DocumentFacade;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DirectiveReportFacade implements DocumentFacade
{
	private DirectiveExcelFactory $factory;

	public function __construct(DirectiveExcelFactory $factory)
	{
		$this->factory = $factory;
	}

	public function createExcelWorkSheet(Worksheet $sheet): Worksheet
	{
		return $this->factory->create($sheet);
	}
}