<?php
namespace SED\Report\Documents\Result;

use SED\Report\Documents\BaseExcelFactory;

class ResultExcelFactory extends BaseExcelFactory
{
	private ResultFactory $factory;
	protected int $top_indentation = 3;

	public function __construct(ResultFactory $factory)
	{
		$this->factory = $factory;
	}

	protected function getDocuments(): \Illuminate\Support\Collection
	{
		return $this->factory->getAll();
	}

	protected function getPropertyMapping(): array
	{
		return [
			'department' => 'A',
			'user' => 'B',
			'report_sum' => 'C',
			'exceptions' => 'D',
			'result_sum' => 'E',
			'fine_directive_author_on_request_change' => 'F',
			'fine_directive_executor_on_execution' => 'G',
			'fine_directive_controller_on_control' => 'H',
			'fine_esz_initiator_on_approval' => 'I',
			'fine_esz_initiator_on_resolution' => 'J',
			'fine_esz_reciver_on_resolution' => 'K',
			'fine_reviewer_on_review' => 'L',
		];
	}
}