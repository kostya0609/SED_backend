<?php
namespace SED\Documents\ESZ\Dto;

use Illuminate\Support\Collection;
use SED\Documents\ESZ\Models\Esz;

class GetByIdESZDto
{
	public Esz $document;
	public Collection $rights;

	public function __construct(Esz $document, Collection $rights)
	{
		$this->document = $document;
		$this->rights = $rights;
	}
}