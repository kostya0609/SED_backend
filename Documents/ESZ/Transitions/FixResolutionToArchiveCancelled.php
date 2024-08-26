<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;

class FixResolutionToArchiveCancelled extends BaseTransition
{	/**
	 * @inheritDoc
	 */
	protected function getFromStatusId(): int
	{
		return Status::FIX_RESOLUTION;
	}

	/**
	 * @inheritDoc
	 */
	protected function getToStatusId(): int
	{
		return Status::ARCHIVE_CANCELLED;
	}
}