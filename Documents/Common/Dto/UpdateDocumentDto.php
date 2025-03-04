<?php
namespace SED\Documents\Common\Dto;

class UpdateDocumentDto
{
	public string $theme;
	public int $initiator_id;
	public string $status_title;
	public int $status_id;
	public ?array $participants;

	/**
	 * @deprecated свойство больше не используется
	 */
	public string $number;
}