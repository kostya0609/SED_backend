<?php
namespace SED\Documents\Common\Controllers\v1;

use Illuminate\Http\Request;
use SED\Common\Controllers\BaseController;
use SED\Documents\Common\Services\BasedCreation\BasedCreationService;

class BasedCreationController extends BaseController
{
	private BasedCreationService $service;

	public function __construct(BasedCreationService $service)
	{
		$this->service = $service;
	}

	public function createFrom(Request $request)
	{
		$validated = (object) $request->validate(
			[
				'based_document_id' => 'required|integer',
				'template_ids' => 'required|array',
			],
			[
				'based_document_id.required' => 'Необходимо указать id базового документа',
				'based_document_id.integer' => 'Id базового документа должно быть целым числом',
				'template_ids.required' => 'Необходимо указать id шаблонов документов',
				'template_ids.array' => 'Id шаблонов документов должны быть массивом',
			]
		);

		$documents = $this->service->createFrom($validated->based_document_id, $validated->template_ids);
		return $this->sendResponse($documents);
	}
}