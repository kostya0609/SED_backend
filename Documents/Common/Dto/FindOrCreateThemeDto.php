<?php
namespace SED\Documents\Common\Dto;

class FindOrCreateThemeDto
{
	public ?int $id;
	public ?string $title;

	public static function create(?int $id, ?string $title): self
	{
		$dto = new self();
		$dto->id = $id;
		$dto->title = $title;

		return $dto;
	}
}