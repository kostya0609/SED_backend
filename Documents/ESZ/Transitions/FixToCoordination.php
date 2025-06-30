<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Models\Esz;
use SED\Documents\ESZ\Services\NeedActionService;

class FixToCoordination extends BaseTransition
{
	/**
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

	protected function handle(Esz $esz): Esz
	{
		/** @var NeedActionService */
		$needActionService = \App::make(NeedActionService::class);

		$needActionService->delete($esz->initiator->user_id, $esz->id);
		return parent::handle($esz);
	}
}