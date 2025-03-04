<?php
namespace SED\Documents\Directive\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserItemDto;

class UpdateDirectiveDto
{
	public int $document_id;
	public string $executed_at;
	public string $content;
	public ?string $portfolio;
	public UserItemDto $author;
	public Collection $executors;
	public Collection $controllers;
	public Collection $observers;

	public function __construct()
	{
		$this->executors = new Collection();
		$this->controllers = new Collection();
		$this->observers = new Collection();
	}

	public function setAuthor(int $user_id, bool $can_deletable): self
	{
		$this->author = new UserItemDto($user_id, $can_deletable);
		return $this;
	}

	public function addExecutor(int $user_id, bool $can_deletable = true): self
	{
		$this->executors->push(new UserItemDto($user_id, $can_deletable));
		return $this;
	}

	public function addController(int $user_id, bool $can_deletable = true): self
	{
		$this->controllers->push(new UserItemDto($user_id, $can_deletable));
		return $this;
	}

	public function addObserver(int $user_id, bool $can_deletable = true): self
	{
		$this->observers->push(new UserItemDto($user_id, $can_deletable));
		return $this;
	}
}