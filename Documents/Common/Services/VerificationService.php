<?php
namespace SED\Documents\Common\Services;

use Illuminate\Database\Eloquent\Builder;
use SED\Common\Config\SEDConfig;
use App\Modules\Roles\Enums\DynamicRole;
use App\Modules\Accesses\Actions\GetAction;
use SED\Documents\Common\Models\Participant;
use SED\Documents\ESZ\Config\ESZConfig;
use SED\Documents\Review\Config\ReviewConfig;
use App\Modules\Roles\Facades\DynamicRoleFacade;
use SED\Documents\Directive\Config\DirectiveConfig;
use App\Modules\Processes\Facades\ParticipantFacade;

class VerificationService
{
	public function checkListAccess($model, $user_id)
	{
		$rights = GetAction::rightsUserModule($user_id, SEDConfig::getModuleName());

		if (in_array('full_access', $rights['rights'])) {
			return $model;
		}

		$initiator_ids = [$user_id];

		$subordinates = DynamicRoleFacade::getUsersByRoleId(DynamicRole::SUBORDINATES, $user_id)->pluck('id')->toArray();
		$initiator_ids = array_merge($initiator_ids, $subordinates);

		$additional_rights_users = GetAction::getAdditionalRights($user_id, SEDConfig::getModuleName())->pluck('id')->toArray();
		$initiator_ids = array_merge($initiator_ids, $additional_rights_users);

		$initiator_ids = array_unique($initiator_ids);

		$document_ids_from_process = $this->getDocumentIdsFromProcess($user_id);

		$user_document_ids = Participant::query()
			->select(['document_id'])
			->distinct()
			->whereIn('user_id', $initiator_ids)
			->pluck('document_id');


		$document_ids = array_merge(
			$user_document_ids->values()->toArray(),
		);
		$document_ids = array_unique($document_ids);

		$model = $model
			->where(function (Builder $query) use ($initiator_ids, $document_ids_from_process, $document_ids) {
				$query
					->orWhereIn('initiator_id', $initiator_ids)
					->orWhereIn('document_id', $document_ids_from_process)
					->orWhereIn('id', $document_ids);
			});

		return $model;
	}

	private function getDocumentIdsFromProcess(int $user_id): array
	{
		return array_merge(
			ParticipantFacade::getParticipantDocumentIds(DirectiveConfig::getModuleName(), $user_id)->values()->toArray(),
			ParticipantFacade::getParticipantDocumentIds(ReviewConfig::getModuleName(), $user_id)->values()->toArray(),
			ParticipantFacade::getParticipantDocumentIds(ESZConfig::getModuleName(), $user_id)->values()->toArray(),
		);
	}
}
