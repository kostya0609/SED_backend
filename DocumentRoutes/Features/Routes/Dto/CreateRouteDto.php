<?php
namespace SED\DocumentRoutes\Features\Routes\Dto;

class CreateRouteDto
{
	public string $title;
	public ?int $direction_id = null;
	public ?int $group_id = null;
	public int $user_id;
	public ?string $description;
	public int $partition_id;
	public array $departments;
	public bool $is_active;
}