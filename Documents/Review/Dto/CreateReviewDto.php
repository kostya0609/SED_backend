<?php
namespace SED\Documents\Review\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Common\Dto\UserItemDto;

/**
 * @property Collection<UserItemDto> $receivers
 * @property Collection<UserItemDto> $observers
 */
class CreateReviewDto
{
	public string $content;
	public ?string $portfolio;
	public Collection $receivers;
	public int $user_id;
	public ?int $tmp_doc_id;
	public ?string $theme_title;
	public ?int $parent_document_id = null;
	public ?int $document_hierarchy_id = null;

    public ?int $root_tmp_id;

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
