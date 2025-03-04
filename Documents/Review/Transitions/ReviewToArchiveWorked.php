<?php
namespace SED\Documents\Review\Transitions;

use SED\Documents\Review\Enums\Status;
use SED\Documents\Review\Models\Review;
use SED\DocumentRoutes\AutomationItemFacade;

class ReviewToArchiveWorked extends BaseTransition
{
	public function handle(Review $review): Review
	{
		if (!is_null($review->tmp_doc_id)) {
			AutomationItemFacade::autorun($review->tmp_doc_id, $review->common_document_id);
		}

		$review->status_id = Status::ARCHIVE_WORKED;
		$review->save();

		return $this->execute($review);
	}
}