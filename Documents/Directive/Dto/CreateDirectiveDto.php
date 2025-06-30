<?php
namespace SED\Documents\Directive\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserItemDto;

class CreateDirectiveDto
{
	public string $executed_at;
	public string $content;
	public ?string $portfolio = null;
	public int $creator_id;
	public ?UserItemDto $author = null;
	public Collection $executors;
	public Collection $controllers;
	public Collection $observers;
	public ?int $tmp_doc_id;
	public ?string $theme_title = null;
	public ?int $parent_document_id = null;
	public ?int $document_hierarchy_id = null;

    public ?int $root_tmp_id;


    public function __construct()
	{
		$this->executors = new Collection();
		$this->controllers = new Collection();
		$this->observers = new Collection();
	}

	public function setAuthor(int $user_id, bool $can_deletable = true): self
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
