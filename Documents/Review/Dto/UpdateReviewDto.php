<?php
namespace SED\Documents\Review\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserItemDto;

/**
 * @property Collection<UserItemDto> $receivers
 * @property Collection<UserItemDto> $observers
 */
class UpdateReviewDto
{
	public int $document_id;
	public string $content;
	public ?string $portfolio;
	public Collection $receivers;

	public function __construct()
	{
		$this->receivers = new Collection();
	}

	public function addReceiver(int $user_id, bool $can_deletable = true): self
	{
		$this->receivers->push(new UserItemDto($user_id, $can_deletable));
		return $this;
	}
}
