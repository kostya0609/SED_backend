<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Dto;

class EditDocTmpDto
{
	public int $id;
	public string $title;

	/**
	 * @var int[]
	 */
	public array $parents;
	public int $route_id;
	public int $type_id;	
	public array $data;
	public bool $is_start;
	public bool $is_active;
	public ?string $requirements;
	public int $user_id;
}
