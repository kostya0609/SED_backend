<?php
namespace SED\BasedCreation;

use SED\Documents\Common\Models\Document;
use SED\DocumentRoutes\Features\DocumentTemplates\Models\DocumentTemplate;

interface BasedCreationInterface
{
	public function create(?Document $base_document = null, DocumentTemplate $document_template, ?int $initiator_id = null): Document;
}