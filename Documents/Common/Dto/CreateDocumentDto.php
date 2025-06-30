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

    //для автоматизации
    public ?int $root_tmp_id;


    /**
	 * @deprecated Больше не используется для создания иерархии документов, так как используется отдельный модуль иерархии
	 */
	public ?int $parent_document_id;
	public ?DocumentTemplate $template_document;
	public ?int $tmp_doc_id;
	public string $content;
	public int $document_hierarchy_id;
}
