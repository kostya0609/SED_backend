<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;

class PreparationToInWork extends BaseTransition
{
	public function handle(Directive $directive): Directive
	{
		$directive->status_id = Status::EXECUTION_IN_WORK;
		$directive->save();

		return parent::execute($directive);
	}
}