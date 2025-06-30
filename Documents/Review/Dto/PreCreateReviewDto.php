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
	public ?string $portfolio = null;
	public Collection $receivers;
	public int $user_id;
	public ?int $tmp_doc_id = null;
	public ?string $theme_title = null;
	public ?int $parent_document_id = null;
	public ?int $document_hierarchy_id = null;

    public ?int $root_tmp_id = null;

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
