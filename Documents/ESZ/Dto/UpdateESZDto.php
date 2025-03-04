<?php
namespace SED\Documents\ESZ\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserItemDto;

/**
 * @property Collection<UserItemDto> $receivers
 * @property Collection<UserItemDto> $observers
 */
class UpdateESZDto
{
	public int $document_id;
	public string $content;
	public ?string $portfolio;
	public UserItemDto $signatory;
	public Collection $receivers;
	public Collection $observers;

	public function __construct()
	{
		$this->receivers = new Collection();
		$this->observers = new Collection();
	}

	public function setSignatory(int $user_id, bool $can_deletable = true): self
	{
		$this->signatory = new UserItemDto($user_id, $can_deletable);
		return $this;
	}

	public function addReceiver(int $user_id, bool $can_deletable = true): self
	{
		$this->receivers->push(new UserItemDto($user_id, $can_deletable));
		return $this;
	}

	public function addObserver(int $user_id, bool $can_deletable = true): self
	{
		$this->observers->push(new UserItemDto($user_id, $can_deletable));
		return $this;
	}
}