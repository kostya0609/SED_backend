<?php
namespace SED\Documents\Directive\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserRoleAggregator;

/**
 * @property Collection<UserRoleAggregator> $executors
 * @property Collection<UserRoleAggregator> $controllers
 * @property Collection<UserRoleAggregator> $observers
 */
class PreCreateDirectiveDto
{
	public string $executed_at;
	public string $content;
	public ?string $portfolio;
	public int $creator_id;
	public ?UserRoleAggregator $author = null;
	public Collection $executors;
	public Collection $controllers;
	public Collection $observers;
	public ?int $tmp_doc_id;
	public ?string $theme_title;
	public ?int $parent_document_id = null;

	public function __construct()
	{
		$this->executors = new Collection();
		$this->controllers = new Collection();
		$this->observers = new Collection();
	}

	public function setAuthor(array $author): self
	{
		$this->author = new UserRoleAggregator($author);
		return $this;
	}

	public function addExecutor(array $executor): self
	{
		$this->executors->push(new UserRoleAggregator($executor));
		return $this;
	}

	public function addController(array $controller): self
	{
		$this->controllers->push(new UserRoleAggregator($controller));
		return $this;
	}

	public function addObserver(array $observer): self
	{
		$this->observers->push(new UserRoleAggregator($observer));
		return $this;
	}
}