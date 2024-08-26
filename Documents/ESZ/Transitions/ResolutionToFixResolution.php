<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;

class ResolutionToFixResolution extends BaseTransition
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
		return Status::FIX_RESOLUTION;
	}
}