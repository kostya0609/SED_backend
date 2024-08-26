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

			'receivers' => 'required|array',
			'receivers.*' => 'required|integer',

			'tmp_doc_id' => 'required_without_all:theme_title|nullable|integer',
			'theme_title' => 'required_without_all:tmp_doc_id|nullable|string',

			'user_id' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'theme.required' => 'Темы не были переданы!',
			'theme_id.integer' => 'Идентификатор темы должен быть целым числом!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',

			'receivers.required' => 'Идентификаторы получающих не были переданы!',
			'receivers.*.integer' => 'Идентификатор получающего должен быть целым числом!',

			'tmp_doc_id.required_without_all' => 'Идентификатор шаблона документа и заголовок темы не были переданы!',
			'theme_title.required_without_all' => 'Тема и идентификатор шаблона документа не были переданы!',
		];
	}
}
