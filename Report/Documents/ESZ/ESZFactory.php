<?php
namespace SED\Report\Documents\ESZ;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use SED\Documents\ESZ\Enums\Status;
use SED\Report\Interfaces\DocumentFactory;

class ESZFactory implements DocumentFactory
{
	public function getAll(): Collection
	{
		return \SED\Documents\ESZ\Models\Esz::query()
			->whereIn('status_id', [
				Status::FIX,
				Status::COORDINATION,
				Status::FIX_SIGNING,
				Status::SIGNING,
				Status::FIX_RESOLUTION,
				Status::RESOLUTION,
			])
			->whereDate('created_at', '<', Carbon::now()->subDays(30)->toDateTimeString())
			->get()
			->mapInto(Esz::class);
	}
}