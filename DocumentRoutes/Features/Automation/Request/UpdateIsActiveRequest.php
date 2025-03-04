<?php
namespace SED\DocumentRoutes\Features\Automation\Request;

use SED\Common\Requests\BaseRequest;

class UpdateIsActiveRequest extends BaseRequest
{
	public function rules(): array
	{
		return [
			'id' => 'required|integer',
			'tmp_doc_id' => 'required|integer',
			'is_active' => 'required|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'id.required' => 'Идентификатор настройки обязателен!',
			'id.integer' => 'Идентификатор настройки должен быть целым числом!',
			'is_active.required' => 'Поле "Активна" обязательно!',
			'is_active.boolean' => 'Поле "Активна" должно быть булевым значением!',
			'tmp_doc_id.required' => 'Идентификатор шаблона документа обязателен!',
			'tmp_doc_id.integer' => 'Идентификатор шаблона документа должен быть целым числом!',
		];
	}
}