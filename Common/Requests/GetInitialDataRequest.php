<?php
namespace SED\Common\Requests;

class GetInitialDataRequest extends BaseRequest
{
	public function rules(): array
	{
		return [
			'user_id' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'user_id.required' => 'Пользователь не указан',
			'user_id.integer' => 'Пользователь должен быть целым числом',
		];
	}
}