<?php
namespace SED\Documents\Common\Services;

use Illuminate\Database\Eloquent\Builder;
use SED\Common\Config\SEDConfig;
use App\Modules\Roles\Enums\DynamicRole;
use App\Modules\Accesses\Actions\GetAction;
use SED\Documents\Common\Enums\DocumentType;
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
			->where(function (Builder $query) use ($initiator_ids, $user_id, $document_ids) {
				$query
					->orWhereIn('initiator_id', $initiator_ids)
					->orWhere(function (Builder $query) use ($user_id) {
						$query
							->where(function (Builder $query) use ($user_id) {
								$query
									->where('type_id', DocumentType::DIRECTIVE)
									->whereIn('document_id', ParticipantFacade::getParticipantDocumentIds(DirectiveConfig::getModuleName(), $user_id)->values()->toArray());
							})
							->orWhere(function (Builder $query) use ($user_id) {
								$query
									->where('type_id', DocumentType::REVIEW)
									->whereIn('document_id', ParticipantFacade::getParticipantDocumentIds(ReviewConfig::getModuleName(), $user_id)->values()->toArray());
							})
							->orWhere(function (Builder $query) use ($user_id) {
								$query
									->where('type_id', DocumentType::ESZ)
									->whereIn('document_id', ParticipantFacade::getParticipantDocumentIds(ESZConfig::getModuleName(), $user_id)->values()->toArray());
							});
					})
					->orWhereIn('id', $document_ids);
			});

		return $model;
	}
}
