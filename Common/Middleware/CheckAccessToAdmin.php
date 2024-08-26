<?php
namespace SED\Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use SED\Common\Config\SEDConfig;
use App\Modules\Accesses\Actions\GetAction;

class CheckAccessToAdmin
{
	public function handle(Request $request, Closure $next)
	{
		$rights = GetAction::rightsUserModule($request->input('user_id'), SEDConfig::getModuleName());

		if (!in_array('full_access', $rights['rights'])) {
			throw new \Exception('Нет прав доступа!');
		}

		return $next($request);
	}
}