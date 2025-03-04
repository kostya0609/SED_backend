<?php
namespace SED\Documents\Review\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserRoleAggregator;

/**
 * @property Collection<UserRoleAggregator> $receivers
 * @property Collection<UserRoleAggregator> $observers
 */
class PreCreateReviewDto
{
	public string $content;
	public ?string $portfolio;
	public Collection $receivers;
	public int $user_id;
	public ?int $tmp_doc_id;
	public ?string $theme_title;
	public ?int $parent_document_id = null;

	public function __construct()
	{
		$this->receivers = new Collection();
		$this->observers = new Collection();
	}
	
	public function addReceiver(array $receiver): self
	{
		$this->receivers->push(new UserRoleAggregator($receiver));
		return $this;
	}
}