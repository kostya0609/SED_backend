<?php
namespace SED\Facades;

use Illuminate\Support\Facades\Facade;
use SED\Documents\Directive\Services\DirectiveService;

class SEDDirectiveFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return DirectiveService::class;
	}
}