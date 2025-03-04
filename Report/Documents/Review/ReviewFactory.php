<?php
namespace SED\Report\Documents\Review;

use Illuminate\Support\Collection;
use SED\Documents\Review\Enums\Status;
use SED\Report\Interfaces\DocumentFactory;

class ReviewFactory implements DocumentFactory
{
	public function getAll(): Collection
	{
		return \SED\Documents\Review\Models\Review::query()
			->where('status_id', Status::REVIEW)
			->whereDate('created_at', '<', \Carbon\Carbon::now()->subDays(5)->toDateTimeString())
			->get()
			->mapInto(Review::class);
	}
}