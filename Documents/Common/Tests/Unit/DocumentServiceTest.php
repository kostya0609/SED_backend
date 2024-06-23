<?php
namespace SED\Documents\Common\Tests\Unit;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;
use Tests\TestCase;
use SED\Documents\Common\Enums\DocumentType;
use SED\Documents\Common\Services\DocumentService;

class DocumentServiceTest extends TestCase
{
	private $documentService;
	private int $year;

	public function setUp(): void
	{
		$this->documentService = resolve(DocumentService::class);
		$this->year = Carbon::now()->year;

		if (!$this->app) {
			$this->refreshApplication();
		}

		$this->setUpTraits();

		foreach ($this->afterApplicationCreatedCallbacks as $callback) {
			call_user_func($callback);
		}

		Facade::clearResolvedInstances();

		Model::setEventDispatcher($this->app['events']);

		$this->setUpHasRun = true;
	}

	private function generateDocumentNumber(string $type, string $abr, int $document_id)
	{
		return "$type-($abr)-$this->year-{$document_id}";
	}

	public function testGenerateEszDocumentNumber()
	{
		$document_id = 123;
		$type_id = DocumentType::ESZ;
		$user_id = 14956;
		$abr = 'ДПУ WEB';
		$type = 'ЭСЗ';

		$expectedResult = $this->generateDocumentNumber($type, $abr, $document_id);
		$result = $this->documentService->generateDocumentNumber($document_id, $type_id, $abr);

		$this->assertEquals($expectedResult, $result);
	}

	public function testGenerateDirectiveDocumentNumber()
	{
		$document_id = 123;
		$type_id = 2;
		$user_id = 14351;
		$abr = 'ДПУ 1С';
		$type = 'П';

		$expectedResult = $this->generateDocumentNumber($type, $abr, $document_id);
		$result = $this->documentService->generateDocumentNumber($document_id, $type_id, $abr);

		$this->assertEquals($expectedResult, $result);
	}

	public function testGenerateReviewDocumentNumber()
	{
		$document_id = 123;
		$type_id = 3;
		$user_id = 13005;
		$abr = 'ДПУ ОАИТ';
		$type = 'О';

		$expectedResult = $this->generateDocumentNumber($type, $abr, $document_id);
		$result = $this->documentService->generateDocumentNumber($document_id, $type_id, $abr);

		$this->assertEquals($expectedResult, $result);
	}

	public function testGenerateDocumentNumberWithInvalidTypeId()
	{
		$document_id = 123;
		$type_id = -123;
		$user_id = 13005;
		$abr = '';

		$this->expectException(\Exception::class);
		$this->documentService->generateDocumentNumber($document_id, $type_id, $abr);
	}

	public function testGenerateDocumentNumberWithEmptyInputs()
	{
		$document_id = 0;
		$type_id = 0;
		$user_id = 0;
		$abr = '';

		$this->expectException(\Exception::class);
		$this->documentService->generateDocumentNumber($document_id, $type_id, $abr);
	}
}