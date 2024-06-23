<?php
namespace SED\Documents\Directive\Services;

use SED\Documents\Directive\Models\History;
use SED\Documents\Directive\Dto\CreateHistoryDto;

class HistoryService
{
	public function create(CreateHistoryDto $dto): History
	{
		$history = new History((array) $dto);
		$history->save();

		return $history;
	}
}