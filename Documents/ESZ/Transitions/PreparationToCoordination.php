<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Models\Esz;

class PreparationToCoordination extends BaseTransition
{
	/**
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
		return Status::COORDINATION;
	}
}