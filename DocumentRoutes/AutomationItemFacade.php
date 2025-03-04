<?php
namespace SED\DocumentRoutes;

use Illuminate\Support\Facades\Facade;
use SED\DocumentRoutes\Features\Automation\Services\AutomationItemService;

class AutomationItemFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return AutomationItemService::class;
	}
}