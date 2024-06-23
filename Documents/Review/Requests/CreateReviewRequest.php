<?php
namespace SED\Documents\Review\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Review\Dto\CreateUpdateReviewDto;

class CreateReviewRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return CreateUpdateReviewDto::class;
	}

	public function createDto(): CreateUpdateReviewDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
            'theme_id' => 'required|integer',

			'content' => 'required|string',

			'responsible_id' => 'required|integer',

			'receivers' => 'required|array',
			'receivers.*' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'theme.required' => 'Темы не были переданы!',
            'theme_id.integer' => 'Идентификатор темы должен быть целым числом!',

			'content.required' => 'Содержание не было передано!',
            'content.string' => 'Содержание должно быть строкой!',

			'responsible_id.required' => 'Идентификатор инициатора не был передан!',
            'responsible_id.integer' => 'Идентификатор инициатора должен быть целым числом!',

			'receivers.required' => 'Идентификаторы получающих не были переданы!',
            'receivers.*.integer' => 'Идентификатор получающего должен быть целым числом!',
		];
	}
}
