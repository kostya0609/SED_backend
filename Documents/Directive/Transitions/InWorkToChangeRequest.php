<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;

class InWorkToChangeRequest extends BaseTransition
{
	public function handle(Directive $directive): Directive
	{
		$directive->status_id = Status::EXECUTION_CHANGE_REQUEST;
		$directive->save();

		return parent::execute($directive);
	}
}