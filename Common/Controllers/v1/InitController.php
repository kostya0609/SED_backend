<?php
namespace SED\Common\Controllers\v1;

use App\Modules\Accesses\Actions\GetAction;
use App\Modules\Users\Facades\UserFacade;
use SED\Common\Config\SEDConfig;
use SED\Common\Controllers\BaseController;
use SED\Common\Requests\GetInitialDataRequest;

class InitController extends BaseController
{
	public function getInitialData(GetInitialDataRequest $request)
	{
		$user_id = $request->user_id;
		$user = UserFacade::getById($user_id);
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());

		return $this->sendResponse([
			'user' => $user,
			'rights' => $rights,
		]);
	}
}