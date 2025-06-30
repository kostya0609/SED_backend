<?php
namespace SED\Documents\Review\Services;

use App\Modules\Accesses\Actions\GetAction;
use App\Modules\DocumentsHierarchy\DocumentsHierarchyFacade;
use App\Modules\Processes\Facades\ParticipantFacade;
use App\Modules\Roles\Enums\DynamicRole;
use App\Modules\Roles\Facades\DynamicRoleFacade;
use Illuminate\Support\Facades\DB;
use SED\Common\Config\SEDConfig;
use SED\Documents\Directive\Config\DirectiveConfig;
use SED\Documents\Review\Config\ReviewConfig;

class VerificationService
{
	//ниже функция проверяет возможно ли пользователю вообще перейти в документ(в деталку или перейти в редактирование)
	public function checkAccess($user_id, $document, $document_participants)
	{
		$document_id = $document->id;
		$initiator_id = $document->initiator->user_id;

		if ($user_id == $initiator_id) {
			return true;
		}

		//ниже проверка полного доступа
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());
		if (in_array('full_access', $rights['rights'])) {
			return true;
		}

		// ниже проверка является ли он участником из этого документа
		if (in_array($user_id, $document_participants)) {
			return true;
		}

		// ниже проверка на доступ к документу из иерархии
		if (DocumentsHierarchyFacade::checkUserOnHierarchy($user_id, $document->document_hierarchy_id)) {
			return true;
		}

		$document_ids_from_process = $this->getDocumentIdsFromProcess($user_id);
		if (in_array($document_id, $document_ids_from_process)) {
			return true;
		}

		// Проверка наличия подчиненных в списке участников документа
		$subordinates = DynamicRoleFacade::getUsersByRoleId(DynamicRole::SUBORDINATES, $user_id)->pluck('id');
		if ($subordinates->intersect($document_participants)->isNotEmpty()) {
			return true;
		}

		if ($this->isBossOfInitiator($user_id, $initiator_id)) {
			return true;
		}

		if ($this->checkSubuser($user_id, $document->id)) {
			return true;
		}

		// ниже проверка доп прав
		$additional_rights_users = GetAction::getAdditionalRights($user_id, SEDConfig::getModuleName());
		$additional_rights_users = $additional_rights_users->filter(function ($value) use ($initiator_id) {
			return $value['id'] == $initiator_id;
		});

		return $additional_rights_users->isNotEmpty();
	}

	//проверка на доступ к редактированию и прочим изменениям в документе
	public function getDocumentFullAccess($user_id, $initiator_id)
	{
		if ($user_id == $initiator_id || $this->isBossOfInitiator($user_id, $initiator_id)) {
			return true;
		}

		//ниже проверка полного доступа
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());
		if (in_array('full_access', $rights['rights'])) {
			return true;
		}

		// ниже проверка доп прав
		$additional_rights_users = GetAction::getAdditionalRights($user_id, SEDConfig::getModuleName());

		$additional_rights_users = $additional_rights_users->filter(function ($value) use ($initiator_id) {
			return $value['id'] == $initiator_id && $value['full_access'] == true;
		});

		return $additional_rights_users->isNotEmpty();
	}

	/**
	 * Проверяет, является ли пользователь $user_id начальником инициатора $initiator_id
	 */
	private function isBossOfInitiator(int $user_id, int $initiator_id): bool
	{
		if (!$this->allDepartments($user_id))
			return false;

		$supervisor_ids = DynamicRoleFacade::getUsersByRoleId(DynamicRole::HIERARCHY_SUPERVISORS, $initiator_id)->pluck('id')->toArray();
		return in_array($user_id, $supervisor_ids);
	}

	private function getDocumentIdsFromProcess(int $user_id): array
	{
		return ParticipantFacade::getParticipantDocumentIds(ReviewConfig::getModuleName(), $user_id)->values()->toArray();
	}

	/**
	 * Возвращает id департамента, где $user_id начальник, либо null, если он не начальник
	 */
	private function allDepartments(int $user_id): ?int
	{
		return DB::table('b_iblock_section')
			->join('b_uts_iblock_5_section', 'b_iblock_section.ID', '=', 'b_uts_iblock_5_section.VALUE_ID')
			->where([['b_iblock_section.IBLOCK_ID', 5]])
			->select('b_iblock_section.ID', 'b_iblock_section.NAME', 'b_iblock_section.IBLOCK_SECTION_ID', 'b_uts_iblock_5_section.UF_HEAD')
			->where('b_uts_iblock_5_section.UF_HEAD', $user_id)
			->pluck('ID')
			->first();
	}

	private function checkSubuser(int $subuser_id, int $document_id)
	{
		$subQuery = \DB::table('l_accesses_sub_users')
			->select('replace_user_id')
			->where('sub_user_id', $subuser_id)
			->where('module', SEDConfig::getModuleName());

		$sub_query = \DB::table('l_processes')
			->join('l_processes_tmp', 'l_processes_tmp.id', '=', 'l_processes.template_id')
			->join('l_processes_participants', 'l_processes_participants.process_id', '=', 'l_processes.id')
			->where('l_processes_tmp.module_name', DirectiveConfig::getModuleName())
			->where('l_processes.status_id', '>', 2)
			->where('l_processes_participants.status_id', 2)
			->whereIn('l_processes_participants.user_id', $subQuery)
			->where('l_processes.document_id', $document_id)
			->exists();

		return $sub_query;
	}
}
