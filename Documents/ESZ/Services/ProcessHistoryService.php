<?php
namespace SED\Documents\ESZ\Services;

use SED\Documents\ESZ\Dto\CreateProcessHistoryDto;
use SED\Documents\ESZ\Models\ProcessHistory;

class ProcessHistoryService
{
	public function create(CreateProcessHistoryDto $dto): ProcessHistory
	{
		$history = new ProcessHistory((array) $dto);
		$history->save();

		if (!empty($dto->files)) {
			$file_ids = collect($dto->files)->pluck('id');
			$history->files()->attach($file_ids->toArray());
		}

		return $history;
	}
}