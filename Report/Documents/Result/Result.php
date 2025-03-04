<?php
namespace SED\Report\Documents\Result;

use App\Modules\Absence\AbsenceFacade;
use App\Modules\Users\Facades\UserFacade;
use SED\Report\Components\Link;
use SED\Report\Interfaces\Document;

class Result implements Document
{
	public Link $department;
	public Link $user;
	public float $report_sum = 0;
	public ?string $exceptions = null;
	public float $result_sum = 0;
	public float $fine_directive_executor_on_execution = 0;
	public float $fine_directive_controller_on_control = 0;
	public float $fine_directive_author_on_request_change = 0;
	public float $fine_esz_initiator_on_approval = 0;
	public float $fine_esz_initiator_on_resolution = 0;
	public float $fine_esz_reciver_on_resolution = 0;
	public float $fine_reviewer_on_review = 0;

	private int $user_id;

	public function setUserId(int $user_id)
	{
		$this->user_id = $user_id;
	}

	public function getUserId(): int
	{
		return $this->user_id;
	}

	public function calculateReportSum(): void
	{
		$this->report_sum = array_sum(
			[
				$this->fine_directive_author_on_request_change,
				$this->fine_directive_executor_on_execution,
				$this->fine_directive_controller_on_control,
				$this->fine_esz_initiator_on_approval,
				$this->fine_esz_initiator_on_resolution,
				$this->fine_esz_reciver_on_resolution,
				$this->fine_reviewer_on_review,
			]
		);
	}

	public function calculateResultSumAndExceptions(): void
	{
		$absence = AbsenceFacade::getCurrentAbsence($this->getUserId());
		$user = UserFacade::getById($this->getUserId());

		if (!$user->is_active) {
			$this->exceptions = 'Уволен';
			$this->result_sum = 0;
			return;
		}

		if ($user->is_probation) {
			$this->exceptions = 'Испытательный срок';
            $this->result_sum = 0;
            return;
		}

		if (is_null($absence)) {
			$this->result_sum = $this->report_sum;
			return;
		}

		$this->exceptions = $absence->type->title;

		if ($absence->isVacation() || $absence->isAssignment() || $absence->isSickLeave() || $absence->isDecret()) {
			$this->result_sum = 0;
			return;
		}

		$this->result_sum = $this->report_sum;
	}
}