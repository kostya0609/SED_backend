<?php
namespace SED\Documents\ESZ\Transitions;

use SED\Documents\ESZ\Enums\Status;
use SED\Documents\ESZ\Models\Esz;
use SED\Documents\ESZ\Services\NeedActionService;

class FixResolutionToArchiveCancelled extends BaseTransition
{
	/**
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

	protected function handle(Esz $esz): Esz
	{
		/** @var NeedActionService */
		$needActionService = \App::make(NeedActionService::class);

		$needActionService->delete($esz->initiator->user_id, $esz->id);
		return parent::handle($esz);
	}
}