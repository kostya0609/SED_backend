<?php
namespace SED\Documents\Review\Transitions;

use SED\Documents\Review\Enums\Status;
use SED\Documents\Review\Models\Review;

class PreparationToReview extends BaseTransition
{
	public function handle(Review $review): Review
	{
		$review->status_id = Status::REVIEW;
		$review->save();

		return $this->execute($review);
	}
}