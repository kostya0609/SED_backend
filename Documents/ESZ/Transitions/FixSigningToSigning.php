<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;

class FixSigningToSigning extends BaseTransition
{
	/**
	 * @inheritDoc
	 */
	protected function getFromStatusId(): int
	{
		return Status::FIX_SIGNING;
	}

	/**
	 * @inheritDoc
	 */
	protected function getToStatusId(): int
	{
		return Status::SIGNING;
	}
}