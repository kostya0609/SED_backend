<?php

namespace SED\DocumentRoutes\Features\TemplatePartitions\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\TemplatePartitions\Dto\EditTemplatePartitionDto;

class EditTemplatePartitionRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return EditTemplatePartitionDto::class;
	}

	public function createDto(): EditTemplatePartitionDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string',
			'template_partition_id' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'title.required' => 'Название раздела не было передано!',
		];
	}
}
