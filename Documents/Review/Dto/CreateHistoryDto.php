<?php
namespace SED\Documents\Review\Dto;

class CreateHistoryDto
{
	public int $review_id;
	public int $user_id;
	public string $event;
}