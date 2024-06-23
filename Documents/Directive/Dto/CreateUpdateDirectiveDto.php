<?php
namespace SED\Documents\Directive\Dto;

class CreateUpdateDirectiveDto
{
	public ?int $document_id;
	public int $theme_id;
	public string $executed_at;
	public string $content;
	public ?string $portfolio;
	public int $creator_id;
	public int $author_id;
	public array $executors;
	public array $controllers = [];
	public array $observers = [];
	public int $user_id;
}