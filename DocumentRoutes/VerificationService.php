<?php
namespace SED\DocumentRoutes;

use App\Modules\Accesses\Actions\GetAction;
use SED\Common\Config\SEDConfig;

class VerificationService
{
	public function checkAccess($user_id): bool
	{		
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());

		if (in_array('full_access', $rights['rights'])) {
			return true;
		}	

		return false;
	}	
}
