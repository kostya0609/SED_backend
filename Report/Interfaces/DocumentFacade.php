<?php
namespace SED\Report\Interfaces;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

interface DocumentFacade
{
	public function createExcelWorkSheet(Worksheet $sheet): Worksheet;
}