<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;

class SigningToResolution extends BaseTransition
{
	/**
	 * @inheritDoc
	 */
	protected function getFromStatusId(): int
	{
		return Status::SIGNING;
	}

	/**
	 * @inheritDoc
	 */
	protected function getToStatusId(): int
	{
		return Status::RESOLUTION;
	}
}