<?php

namespace SED\DocumentRoutes\Features\TemplatePartitions\Dto;


class CreateTemplatePartitionDto
{
	public string $title;
	public int $route_id;
	public int $user_id;
    public int $parent_id;
    public int $root_id;
    public int $parent_template_id;
    public string $parent_type;
}
