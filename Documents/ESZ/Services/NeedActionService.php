<?php
namespace SED\Documents\ESZ\Services;

use App\Modules\CountControl\Dto\AddNeedActionDto;
use App\Modules\CountControl\Dto\DeleteByDto;
use App\Modules\CountControl\Facades\NeedActionFacade;
use SED\Common\Config\SEDConfig;
use SED\Documents\ESZ\Config\ESZConfig;

class NeedActionService
{
	public function add(int $user_id, int $document_id): void
	{
		$need_action_dto = new AddNeedActionDto();
		$need_action_dto->module = ESZConfig::getModuleName();
		$need_action_dto->parent = SEDConfig::getModuleName();
		$need_action_dto->type = 'module';
		$need_action_dto->user_id = $user_id;
		$need_action_dto->document_id = $document_id;
		NeedActionFacade::add($need_action_dto);
	}

	public function delete(int $user_id, int $document_id): void
	{
		$need_action_dto = new DeleteByDto();
		$need_action_dto->module = ESZConfig::getModuleName();
		$need_action_dto->parent = SEDConfig::getModuleName();
		$need_action_dto->type = 'module';
		$need_action_dto->user_id = $user_id;
		$need_action_dto->document_id = $document_id;
		NeedActionFacade::deleteBy($need_action_dto);
	}
}