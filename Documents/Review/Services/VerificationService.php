<?php
namespace SED\Documents\Review\Services;

use App\Modules\Accesses\Actions\GetAction;
use App\Modules\Processes\Facades\ParticipantFacade;
use App\Modules\ReestrRD\Models\Department;
use App\Modules\ReestrRD\Models\Document;
use App\Modules\ReestrRD\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use SED\Common\Config\SEDConfig;
use SED\Documents\Review\Config\ReviewConfig;
use SED\Documents\Review\Models\Review;

class VerificationService
{
	public static function getDocumentIdsFromProcess(int $user_id): array
	{
		return ParticipantFacade::getParticipantDocumentIds(ReviewConfig::getModuleName(), $user_id)->values()->toArray();
	}

	public static function allDepartments(): Collection
	{
		return DB::table('b_iblock_section')
			->join('b_uts_iblock_5_section', 'b_iblock_section.ID', '=', 'b_uts_iblock_5_section.VALUE_ID')
			->where([['b_iblock_section.IBLOCK_ID', 5]])
			->select('b_iblock_section.ID', 'b_iblock_section.NAME', 'b_iblock_section.IBLOCK_SECTION_ID', 'b_uts_iblock_5_section.UF_HEAD as HEAD')
			->get();
	}

	public static function allUsers(): Collection
	{
		return DB::table('b_user')
			->where('ACTIVE', '=', 'Y')
			->join('b_utm_user', 'b_user.ID', '=', 'b_utm_user.VALUE_ID')
			->select(
				'b_user.ID',
				'b_user.ACTIVE',
				'b_user.NAME',
				'b_user.LAST_NAME',
				'b_user.SECOND_NAME',
				'b_user.XML_ID',
				'b_utm_user.VALUE_INT as DEPARTMENT'
			)
			->where([['b_utm_user.FIELD_ID', 41]])
			->get();
	}

	public static function userDepartment($user_id)
	{
		$depId = DB::table('b_user')
			->join('b_utm_user', 'b_user.ID', '=', 'b_utm_user.VALUE_ID')
			->select(
				'b_user.ID',
				'b_user.ACTIVE',
				'b_user.NAME',
				'b_user.LAST_NAME',
				'b_user.SECOND_NAME',
				'b_user.XML_ID',
				'b_utm_user.VALUE_INT as DEPARTMENT'
			)
			->where([['b_utm_user.FIELD_ID', 41], ['b_user.ID', $user_id]])
			->first()->DEPARTMENT;
		return \App\Modules\Departments\Models\Department::find($depId);
	}

	public static function departmentsHierarchy($dep_id)
	{
		$departmentsId = [$dep_id];
		$allDeps = self::allDepartments();

		$childDepartment = $allDeps
			->whereIn('IBLOCK_SECTION_ID', $dep_id)
			->pluck('ID');

		while ($childDepartment->count() > 0) {
			foreach ($childDepartment as $el) {
				$departmentsId[] = $el;
			}
			$childDepartment = $allDeps
				->whereIn('IBLOCK_SECTION_ID', $childDepartment)
				->pluck('ID');
		}
		return $departmentsId;
	}

	//ниже функция проверяет возможно ли пользователю вообще перейти в документ(в деталку или перейти в редактирование)
	public static function checkAccess($user_id, $documentModel)
	{
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());

		if (
			in_array('full_access', $rights['rights'])
			||
			$documentModel->responsible->user_id == $user_id
			||
			in_array($documentModel->id, self::getDocumentIdsFromProcess($user_id))
		) {
			return true;
		}

		$allDeps = self::allDepartments();
		$isBossDep = $allDeps->firstWhere('HEAD', $user_id) ?? '';
		if ($isBossDep) {
			$depHierarchy = self::departmentsHierarchy($isBossDep->ID);

			if (in_array($documentModel->department_id, $depHierarchy)) {
				return true;
			}
		}
		if ($rights['additional_rights']) {
			$users = $rights['additional_rights']['users'];
			$departments = $rights['additional_rights']['departments'];
			if ($users) {
				foreach ($users as $user) {
					$usersIds[] = $user['id'];

				}
			}
			if ($departments) {
				$departmentsID = [];
				foreach ($departments as $department) {
					if ($department['hierarchy']) {
						$departmentsID = array_merge($departmentsID, self::departmentsHierarchy($department['id']));
					} else {
						$departmentsID[] = $department['id'];
					}
				}

				$departmentUsersID = self::allUsers()
					->whereIn('DEPARTMENT', $departmentsID)
					->pluck('ID')->toArray();
				$usersIds = array_merge($usersIds, $departmentUsersID);
			}

			if (in_array($documentModel->responsible->user_id, $usersIds))
				return true;
		}
		return false;
	}
	
	//проверка на доступ к редактированию и прочим изменениям в документе
	public static function checkFullAccess($user_id, $executor_id)
	{
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());

		if (in_array('full_access', $rights['rights'])) 
			return 1;

		if ($user_id == $executor_id) {
			return 1;
		}
		$allDeps = self::allDepartments();

		$isBossDep = $allDeps->where('HEAD', $user_id)->pluck('ID')->first();

		if ($isBossDep) {
			$departmentsId = [$isBossDep];
			$childDepartment = $allDeps
				->whereIn('IBLOCK_SECTION_ID', $isBossDep)
				->pluck('ID');

			while ($childDepartment->count() > 0) {
				foreach ($childDepartment as $el) {
					$departmentsId[] = $el;
				}
				$childDepartment = $allDeps
					->whereIn('IBLOCK_SECTION_ID', $childDepartment)
					->pluck('ID');
			}

			$usersIds = self::allUsers()
				->whereIn('DEPARTMENT', $departmentsId)
				->pluck('ID')->toArray(); //->toArray()
			$usersIds[] = $user_id;

			if (in_array($executor_id, $usersIds)) {
				return 1;
			}
		}

		//Надо добавить проверку доп прав
		return 0;
	}
}
