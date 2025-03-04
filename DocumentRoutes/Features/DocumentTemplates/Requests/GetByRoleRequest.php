<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Requests;

use SED\Common\Requests\BaseRequest;

/**
 * @property int $role_id
 */
class GetByRoleRequest extends BaseRequest
{
	public function rules(): array
	{
		return [
			'role_id' => ['required', 'integer'],
		];
	}

	public function messages(): array
	{
		return [
			'role_id.required' => 'ID роли не было передано!',
			'role_id.integer' => 'ID роли должно быть числом!',
		];
	}
}