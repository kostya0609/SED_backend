<?php
namespace SED\DocumentRoutes;

use Illuminate\Support\Facades\Facade;

class DocumentRoutesFacade extends Facade{

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }
    
}