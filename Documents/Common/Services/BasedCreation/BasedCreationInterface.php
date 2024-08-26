<?php
namespace SED\Documents\Common\Services\BasedCreation;

use SED\Documents\Common\Models\Document;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

interface BasedCreationInterface
{
	public function create(Document $base_document, DocumentTemplate $document_template): Document;
}