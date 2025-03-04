<?php
namespace SED\DocumentRoutes\Features\DocumentTemplates\Requests;

use SED\Common\Requests\BaseRequest;

class UpdateRequirementsRequest extends BaseRequest
{
	public function rules()
	{
		return [
			'id' => 'required|integer',
			'requirements' => 'nullable|string',
		];
	}

	public function messages()
	{
		return [
			'id.required' => 'ID шаблона документа не было передано!',
			'id.integer' => 'ID шаблона документа должно быть целым числом!',

			'requirements.string' => 'Требования должны быть в виде строки!',
		];
	}
}