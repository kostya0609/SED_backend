<?php
namespace SED\Documents\Review\Services;

use SED\Documents\Review\Dto\CreateProcessHistoryDto;
use SED\Documents\Review\Models\ProcessHistory;

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