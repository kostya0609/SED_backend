<?php
namespace SED\Documents\Directive\Dto;

use Illuminate\Support\Collection;
use SED\Documents\Directive\Models\Directive;

class GetByIdDirectiveDto
{
	public Directive $document;
	public Collection $rights;

	public function __construct(Directive $document, Collection $rights)
	{
		$this->document = $document;
		$this->rights = $rights;
	}
}