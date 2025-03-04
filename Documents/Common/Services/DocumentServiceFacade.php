<?php
namespace SED\Documents\Common\Services;

use App\Modules\Accesses\Services\SubUserService;

class DocumentServiceFacade
{
	private DocumentService $documentService;
	private SubUserService $subUserService;

	public function __construct(DocumentService $documentService, SubUserService $subUserService)
	{
		$this->documentService = $documentService;
		$this->subUserService = $subUserService;
	}

	public function myBusiness(int $user_id)
	{
		$data = [
			'module_code' => 'SED',
			'name_module' => 'СЭД 2.0',
			'link_module' => '/sed',
			'user_documents_count' => $this->documentService->getAllCount($user_id),
			'need_action_count' => $this->documentService->getNeedActionCount($user_id),
			'need_action_replace_user_count' => $this->documentService->getNeedActionSubuserCount($user_id),
			'delay_count' => '0',
		];

		return $data;
	}
}