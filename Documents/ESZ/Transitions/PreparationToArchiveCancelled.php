<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;

class PreparationToArchiveCancelled extends BaseTransition
{	/**
	 * @inheritDoc
	 */
	protected function getFromStatusId(): int
	{
		return Status::PREPARATION;
	}

	/**
	 * @inheritDoc
	 */
	protected function getToStatusId(): int
	{
		return Status::ARCHIVE_CANCELLED;
	}
}