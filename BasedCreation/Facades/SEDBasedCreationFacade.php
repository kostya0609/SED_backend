<?php
namespace SED\BasedCreation\Facades;

use Illuminate\Support\Facades\Facade;
use SED\BasedCreation\BasedCreationService;

class SEDBasedCreationFacade extends Facade
{
	public static function getFacadeAccessor()
	{
		return BasedCreationService::class;
	}
}