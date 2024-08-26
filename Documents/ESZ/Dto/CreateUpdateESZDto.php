<?php
namespace SED\Documents\ESZ\Dto;

class CreateUpdateESZDto
{
	public ?int $document_id;
	public string $content;
	public ?string $portfolio;
	public int $signatory_id;
	public array $receivers = [];
	public array $observers = [];
	public int $user_id;
	public ?int $tmp_doc_id;
	public ?string $theme_title;
}