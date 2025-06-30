<?php

namespace SED\DocumentRoutes\Features\TemplatePartitions\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\TemplatePartitions\Dto\CreateTemplatePartitionDto;

class CreateTemplatePartitionRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return CreateTemplatePartitionDto::class;
	}

	public function createDto(): CreateTemplatePartitionDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
			'title' => 'required|string',
		];
	}

	public function messages(): array
	{
		return [
			'title.required' => 'Название раздела не было передано!',
		];
	}
}
