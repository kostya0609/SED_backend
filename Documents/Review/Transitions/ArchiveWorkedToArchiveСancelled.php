<?php
namespace SED\Documents\Review\Transitions;

use SED\Documents\Review\Enums\Status;
use SED\Documents\Review\Models\Review;

class ArchiveWorkedToArchiveСancelled extends BaseTransition
{
	public function handle(Review $review): Review
	{
		$review->status_id = Status::ARCHIVE_CANCELLED;
		$review->save();

		$review = $review->fresh(['status']);

		return $this->execute($review);
	}
}
