<?php
namespace SED\Documents\ESZ\Services;

use SED\Documents\ESZ\Models\History;
use SED\Documents\ESZ\Dto\CreateHistoryDto;

class HistoryService
{
	public function create(CreateHistoryDto $dto): History
	{
		$history = new History((array) $dto);
		$history->save();

		return $history;
	}
}