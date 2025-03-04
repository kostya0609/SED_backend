<?php
namespace SED\Documents\Review\Dto;

class CreateProcessHistoryDto
{
	public int $review_id;
	public int $user_id;
	public ?int $subuser_id;
	public string $event;
	public ?string $comment;
	public ?array $files;
	public ?string $process_template_name;	
}