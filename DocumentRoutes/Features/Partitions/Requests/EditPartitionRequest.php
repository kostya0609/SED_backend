<?php

namespace SED\DocumentRoutes\Features\Partitions\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\Partitions\Dto\EditPartitionDto;

class EditPartitionRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return EditPartitionDto::class;
	}

	public function createDto(): EditPartitionDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
			'id' => 'required|integer',
			'parent_id' => 'required_without:title|integer|nullable',
			'title' => 'required_without:parent_id|string',
		];
	}

	public function messages(): array
	{
		return [
			'id.required' => 'ID раздела не был передан!',
			'id.integer' => 'ID раздела должен быть целым числом!',

			'parent_id.integer' => 'ID родительского раздела должен быть целым числом!',
			'title.string' => 'Название раздела должно быть строкой!',

			'title.required_without' => 'Название раздела обязательно!',
			'parent_id.required_without' => 'Id родительского раздела обязательно!',
		];
	}
}
