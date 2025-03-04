<?php
namespace SED\Documents\Common\Dto;

class UserItemDto
{
	public int $user_id;
	public bool $can_deletable = true;

	public function __construct(int $user_id, bool $can_deletable = true)
	{
		$this->user_id = $user_id;
		$this->can_deletable = $can_deletable;
	}
}