<?php
namespace SED\Documents\Review\Dto;

/**
 * @property array<\SED\Documents\Common\Dto\FindOrCreateThemeDto> $theme
 */
class CreateUpdateReviewDto
{
	public ?int $document_id;
	public string $content;
	public ?string $portfolio;
	public array $receivers;
	public ?int $tmp_doc_id;
	public ?string $theme_title;
	public int $user_id;
}
