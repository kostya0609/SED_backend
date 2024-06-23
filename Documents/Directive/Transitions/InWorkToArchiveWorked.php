<?php
namespace SED\Documents\Directive\Transitions;

use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;
use SED\Documents\Directive\Transitions\BaseTransition;

class InWorkToArchiveWorked extends BaseTransition
{
	public function handle(Directive $directive): Directive {
		$directive->status_id = Status::ARCHIVE_WORKED;
        $directive->save();

        return parent::execute($directive);
	}
}