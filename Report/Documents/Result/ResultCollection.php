<?php
namespace SED\Report\Documents\Result;

use Illuminate\Support\Collection;

class ResultCollection
{
	/**
	 * @var Collection<Result>
	 */
	private Collection $items;

	public function __construct()
	{
		$this->items = new Collection();
	}

	public function push(Result $result): Result
	{
		/**
		 * @var Result $item
		 */
		foreach ($this->items as $item) {
			if ($item->getUserId() === $result->getUserId()) {
				$item->fine_directive_author_on_request_change += $result->fine_directive_author_on_request_change;
				$item->fine_directive_executor_on_execution += $result->fine_directive_executor_on_execution;
				$item->fine_directive_controller_on_control += $result->fine_directive_controller_on_control;
				$item->fine_esz_initiator_on_approval += $result->fine_esz_initiator_on_approval;
				$item->fine_esz_initiator_on_resolution += $result->fine_esz_initiator_on_resolution;
				$item->fine_esz_reciver_on_resolution += $result->fine_esz_reciver_on_resolution;
				$item->fine_reviewer_on_review += $result->fine_reviewer_on_review;

				$item->calculateReportSum();
				$item->calculateResultSumAndExceptions();

				return $item;
			}
		}

		$result->calculateReportSum();
		$result->calculateResultSumAndExceptions();
		$this->items->push($result);
		return $result;
	}

	public function getItems(): Collection
	{
		return $this->items;
	}
}