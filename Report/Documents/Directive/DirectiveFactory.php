<?php
namespace SED\Report\Documents\Directive;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use SED\Documents\Directive\Enums\Status;
use SED\Report\Interfaces\DocumentFactory;

class DirectiveFactory implements DocumentFactory
{
	public function getAll(): Collection
	{
		return \SED\Documents\Directive\Models\Directive::query()
			->whereIn('status_id', [
				Status::EXECUTION_CHANGE_REQUEST,
				Status::EXECUTION_IN_WORK,
				Status::EXECUTION_CONTROL,
			])
			->whereDate('executed_at', '>', \Carbon\Carbon::now()->toDateTimeString())
			->where(function (Builder $builder) {
				$builder
					->whereDate('execution_control_date', '<=', \Carbon\Carbon::now()->subDays(3)->toDateTimeString())
					->orWhereNull('execution_control_date');
			})
			->get()
			->mapInto(Directive::class);
	}
}