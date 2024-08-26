<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Dto;

class CreateDocTmpDto
{
	public string $title;
	public ?int $parent_id;
	public int $route_id;
	public int $type_id;	
	public array $data;
	public bool $is_start;
	public bool $is_active;
	public ?string $requirements;
	public int $user_id;
}
