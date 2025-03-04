<?php
namespace SED\Documents\Common\Dto;

use SED\DocumentRoutes\DocumentTemplate;

class CreateDocumentDto
{
	public int $document_id;
	public string $number;
	public int $type_id;
	public string $theme;
	public int $initiator_id;
	public string $status_title;
	public int $status_id;
	public array $participants;
	public ?int $parent_document_id;
	public ?DocumentTemplate $template_document;
	public ?int $tmp_doc_id;
}