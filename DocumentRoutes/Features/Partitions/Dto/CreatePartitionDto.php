<?php
namespace SED\DocumentRoutes\Features\Partitions\Dto;

class CreatePartitionDto
{
	public string $title;
	public ?int $parent_id;
	public int $user_id;
	public ?bool $is_active;
}