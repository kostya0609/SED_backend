<?php

namespace SED\DocumentRoutes\Features\TemplatePartitions\Dto;


class EditTemplatePartitionDto
{
	public string $title;
	public int $route_id;
	public int $user_id;
    public int $root_id;
    public int $parent_id;
    public int $template_partition_id;
}
