<?php
namespace SED\Common\Controllers\v1;

use App\Modules\Accesses\Facades\SubUserFacade;
use SED\Common\Controllers\BaseController;

class SubuserController extends BaseController
{
	public function getAllSubusers()
	{			
		return $this->sendResponse(SubUserFacade::getAllSubUsers());
	}
}