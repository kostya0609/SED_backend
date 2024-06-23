<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;

class ChangeRequestToArchiveCancelled extends BaseTransition
{
	public function handle(Directive $directive): Directive
	{
		$directive->status_id = Status::ARCHIVE_CANCELLED;
		$directive->save();

		return parent::execute($directive);
	}
}