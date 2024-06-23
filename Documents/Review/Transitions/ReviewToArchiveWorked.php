<?php
namespace SED\Documents\Review\Transitions;

use SED\Documents\Review\Enums\Status;
use SED\Documents\Review\Models\Review;

class ReviewToArchiveWorked extends BaseTransition
{
	public function handle(Review $review): Review
	{
		$review->status_id = Status::ARCHIVE_WORKED;
		$review->save();

		// Test comment
		
		return $this->execute($review);
	}
}