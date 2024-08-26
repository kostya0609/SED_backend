<?php
namespace SED\Documents\ESZ\Dto;

class CreateProcessHistoryDto
{
	public int $esz_id;
	public int $user_id;
	public string $event;
	public ?string $comment;
	public ?array $files;
}