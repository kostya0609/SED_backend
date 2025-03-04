<?php
namespace SED\DocumentRoutes\Features\Routes\Dto;

class EditRouteDto
{
	public int $id;
	public string $title;
	public ?int $direction_id = null;
	public ?int $group_id = null;
	public ?string $description;
	public ?int $partition_id;
	public array $departments;
	public int $user_id;
	public bool $is_active;
}