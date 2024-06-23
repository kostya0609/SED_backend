<?php
namespace SED\Documents\Review\Dto;

/**
 * @property array<\SED\Documents\Common\Dto\FindOrCreateThemeDto> $theme
 */
class CreateUpdateReviewDto
{
	public ?int $document_id;
	public int $theme_id;
	public string $content;
    public ?string $portfolio;
	public int $responsible_id;
	public array $receivers;
	public int $user_id;
}
