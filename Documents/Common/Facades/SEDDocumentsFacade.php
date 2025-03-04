<?php
namespace SED\Documents\Common\Facades;

use Illuminate\Support\Facades\Facade;
use SED\Documents\Common\Services\DocumentServiceFacade;

class SEDDocumentsFacade extends Facade
{
	protected static function getFacadeAccessor() {
		return DocumentServiceFacade::class;
	}
}