<?php
namespace SED\Report\Documents;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use SED\Report\Documents\Directive\DirectiveReportFacade;
use SED\Report\Documents\ESZ\ESZReportFacade;
use SED\Report\Documents\Result\ResultReportFacade;
use SED\Report\Documents\Review\ReviewReportFacade;

class ReportFactory
{
	private ESZReportFacade $eszReportFacade;
	private DirectiveReportFacade $directiveReportFacade;
	private ReviewReportFacade $reviewReportFacade;
	private ResultReportFacade $resultReportFacade;

	public function __construct(
		ESZReportFacade $eszReportFacade,
		DirectiveReportFacade $directiveReportFacade,
		ReviewReportFacade $reviewReportFacade,
		ResultReportFacade $resultReportFacade
	) {
		$this->eszReportFacade = $eszReportFacade;
		$this->directiveReportFacade = $directiveReportFacade;
		$this->reviewReportFacade = $reviewReportFacade;
		$this->resultReportFacade = $resultReportFacade;
	}

	public function createExcel(Spreadsheet $spreadsheet): Xlsx
	{
		$spreadsheet->setActiveSheetIndex(0);
		$this->eszReportFacade->createExcelWorkSheet($spreadsheet->getActiveSheet());

		$spreadsheet->setActiveSheetIndex(1);
		$this->directiveReportFacade->createExcelWorkSheet($spreadsheet->getActiveSheet());

		$spreadsheet->setActiveSheetIndex(2);
		$this->reviewReportFacade->createExcelWorkSheet($spreadsheet->getActiveSheet());

		$spreadsheet->setActiveSheetIndex(3);
		$this->resultReportFacade->createExcelWorkSheet($spreadsheet->getActiveSheet());

		return new Xlsx($spreadsheet);
	}
}