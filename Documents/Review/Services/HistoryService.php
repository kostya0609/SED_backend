<?php
namespace SED\Documents\Review\Services;

use SED\Documents\Review\Models\History;
use SED\Documents\Review\Dto\CreateHistoryDto;

class HistoryService
{
	public function create(CreateHistoryDto $dto): History
	{
		$history = new History((array) $dto);
		$history->save();

		return $history;
	}
}