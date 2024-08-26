<?php
namespace SED\DocumentRoutes\Features\Routes\Dto;

class CreateRouteDto
{
	public string $title;
	public int $direction_id;
	public int $group_id;
	public int $user_id;
	public ?string $description;
	public int $partition_id;
	public array $departments;
	public bool $is_active;
}