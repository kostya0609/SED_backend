<?php
namespace SED\Documents\Directive\Transitions;

use SED\DocumentRoutes\AutomationItemFacade;
use SED\Documents\Directive\Enums\Status;
use SED\Documents\Directive\Models\Directive;
use SED\Documents\Directive\Transitions\BaseTransition;

class InWorkToArchiveWorked extends BaseTransition
{
	public function handle(Directive $directive): Directive
	{
		$directive->status_id = Status::ARCHIVE_WORKED;
		$directive->save();

		if (!is_null($directive->tmp_doc_id)) {
			AutomationItemFacade::autorun($directive->tmp_doc_id, $directive->common_document_id);
		}

		return parent::execute($directive);
	}
}