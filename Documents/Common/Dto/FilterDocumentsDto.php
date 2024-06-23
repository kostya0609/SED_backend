<?php
namespace SED\Documents\Common\Dto;

class FilterDocumentsDto
{
    public int $limit;

    public int $offset;

    public string $sort;

    public string $order;

    public array $filters;

    public int $user_id;
}