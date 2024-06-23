<?php
namespace SED\Documents\Common\Dto;

class CreateDocumentDto {
	public int $document_id;
	public string $number;
	public int $type_id;
	public string $theme;
	public int $executor_id;
	public string $status_title;
}