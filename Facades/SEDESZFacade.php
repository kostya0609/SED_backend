<?php
namespace SED\Facades;

use Illuminate\Support\Facades\Facade;
use SED\Documents\ESZ\Services\ESZService;

class SEDESZFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return ESZService::class;
	}
}