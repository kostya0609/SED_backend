<?php
namespace SED\Documents\Review\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Review\Dto\CreateUpdateReviewDto;

class UpdateReviewRequest extends BaseRequest
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
			'document_id' => 'required|integer',
			'theme_id' => 'required|integer',

			'content' => 'required|string',

			'user_id' => 'required|integer',

            'receivers' => 'required|array',
            'receivers.*' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'document_id.required' => 'Идентификатор ознакомления не был передан!',
			'document_id.integer' => 'Идентификатор ознакомления должен быть целым числом!',

			'theme_id.required' => 'Идентификатор темы не был передан!',
			'theme_id.integer' => 'Идентификатор темы должен быть целым числом!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

            'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',

            'receivers.required' => 'Идентификаторы получающих не были переданы!',
            'receivers.*.integer' => 'Идентификатор получающего должен быть целым числом!',
        ];
	}
}
