<?php
namespace SED\Report\Documents\Review;

use SED\Report\Documents\BaseExcelFactory;

class ReviewExcelFactory extends BaseExcelFactory
{
	private ReviewFactory $factory;

	public function __construct(ReviewFactory $factory)
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
			'number' => 'A',
			'created_at' => 'B',
			'status_title' => 'C',
			'initiator' => 'D',
			'fine_receivers_on_review' => 'E',
		];
	}
}