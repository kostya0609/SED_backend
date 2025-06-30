<?php

namespace SED\DocumentRoutes\Features\TemplatePartitions\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\TemplatePartitions\Dto\DeleteTemplatePartitionDto;

class DeleteTemplatePartitionRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return DeleteTemplatePartitionDto::class;
	}

	public function createDto(): DeleteTemplatePartitionDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
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
