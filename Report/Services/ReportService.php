<?php
namespace SED\Report\Services;

use SED\Report\Models\Report;
use App\Modules\BsiTable\FilterFacade;
use SED\Report\Documents\ReportFactory;
use App\Modules\File\Facades\FileFacade;

class ReportService
{
	private ReportFactory $factory;

	public function __construct(ReportFactory $factory)
	{
		$this->factory = $factory;
	}

	public function generateExcelReport(): Report
	{
		$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
		$spreadsheet = $reader->load(__DIR__ . '/../template-report.xlsx');

		$time = \Carbon\Carbon::now()->format('Y-m-d H-i-s');
		$filename = "{$time} Ежемесячный отчет по компании.xlsx";

		/** Фикс бага для боевого сервера, когда файл открывается с ошибкой */
		mb_internal_encoding('latin1');

		[$file, $resource] = FileFacade::createFileResource((object) [
			'name' => $filename,
			'extension' => 'xlsx',
			'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		]);

		$report = new Report();
		$report->file_id = $file->id;
		$report->save();

		$this->factory->createExcel($spreadsheet)->save($resource);

		return $report->fresh();
	}

	public function getAll()
	{
		return FilterFacade::sort()
			->filter()
			->getAll(Report::query());
	}
}