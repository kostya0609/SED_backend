<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Models\Esz;
use SED\Documents\ESZ\Enums\Status;
use SED\DocumentRoutes\AutomationItemFacade;

class ResolutionToArchiveWorked extends BaseTransition
{
	/**
	 * @inheritDoc
	 */
	protected function getFromStatusId(): int
	{
		return Status::RESOLUTION;
	}

	/**
	 * @inheritDoc
	 */
	protected function getToStatusId(): int
	{
		return Status::ARCHIVE_WORKED;
	}

	protected function handle(Esz $esz): Esz
	{
		if (!is_null($esz->tmp_doc_id)) {
			AutomationItemFacade::autorun($esz->tmp_doc_id, $esz->common_document_id);
		}

		return parent::handle($esz);
	}
}