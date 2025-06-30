<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Models\Esz;
use SED\Documents\ESZ\Services\NeedActionService;

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

	protected function handle(Esz $esz): Esz
	{
		/** @var NeedActionService */
		$needActionService = \App::make(NeedActionService::class);

		$needActionService->add($esz->initiator->user_id, $esz->id);
		return parent::handle($esz);
	}
}