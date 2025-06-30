<?php
namespace SED\Facades;

use Illuminate\Support\Facades\Facade;
use SED\Documents\Review\Services\ReviewService;

class SEDReviewFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return ReviewService::class;
	}
}