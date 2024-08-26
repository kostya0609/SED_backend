<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;

class FixToCoordination extends BaseTransition
{	/**
	 * @inheritDoc
	 */
	protected function getFromStatusId(): int
	{
		return Status::FIX;
	}

	/**
	 * @inheritDoc
	 */
	protected function getToStatusId(): int
	{
		return Status::COORDINATION;
	}
}