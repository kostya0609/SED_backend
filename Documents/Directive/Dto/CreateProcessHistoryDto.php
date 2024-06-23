<?php
namespace SED\Documents\Directive\Dto;

class CreateProcessHistoryDto
{
	public int $directive_id;
	public int $user_id;
	public string $event;
	public ?string $comment;
	public ?array $files;
}