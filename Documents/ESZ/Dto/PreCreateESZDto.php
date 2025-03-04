<?php
namespace SED\Documents\ESZ\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserRoleAggregator;

/**
 * @property Collection<UserRoleAggregator> $receivers
 * @property Collection<UserRoleAggregator> $observers
 */
class PreCreateESZDto
{
	public string $content;
	public ?string $portfolio;
	public ?UserRoleAggregator $signatory = null;
	public Collection $receivers;
	public Collection $observers;
	public int $user_id;
	public ?int $tmp_doc_id;
	public ?string $theme_title;
	public ?int $parent_document_id = null;

	public function __construct()
	{
		$this->receivers = new Collection();
		$this->observers = new Collection();
	}

	public function setSignatory(array $signatory): self
	{
		$this->signatory = new UserRoleAggregator($signatory);
		return $this;
	}

	public function addReceiver(array $receiver): self
	{
		$this->receivers->push(new UserRoleAggregator($receiver));
		return $this;
	}

	public function addObserver(array $observer): self
	{
		$this->observers->push(new UserRoleAggregator($observer));
		return $this;
	}
}