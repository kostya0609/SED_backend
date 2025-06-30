<?php
namespace SED\Facades;

use Illuminate\Support\Facades\Facade;
use SED\Facades\DocumentBuilder\DocumentBuilder;

class SEDDocumentBuilderFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return DocumentBuilder::class;
	}
}