<?php
namespace SED\Documents\Common\Services;

use Illuminate\Support\Collection;
use SED\Documents\Common\Models\DocumentType;

class DocumentTypeService
{

	public function create(string $title): DocumentType
	{
		$type = new DocumentType();
		$type->title = $title;
		$type->save();

		return $type;
	}

	public function getAll(): Collection
	{
		return DocumentType::all();
	}
}
