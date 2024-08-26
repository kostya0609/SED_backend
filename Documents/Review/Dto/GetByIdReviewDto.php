<?php
namespace SED\Documents\Review\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Review\Models\Review;

class GetByIdReviewDto
{
	public Review $document;
	public Collection $rights;

	public function __construct(Review $document, Collection $rights)
	{
		$this->document = $document;
		$this->rights = $rights;
	}
}