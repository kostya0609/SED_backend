<?php
namespace SED\Documents\Directive\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Directive\Dto\CreateUpdateDirectiveDto;

class UpdateDirectiveRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return CreateUpdateDirectiveDto::class;
	}

	public function createDto(): CreateUpdateDirectiveDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
			'document_id' => 'required|integer',

			'theme_id' => 'required|integer',

			'executed_at' => 'required|date',

			'content' => 'required|string',
			'portfolio' => 'required|string',

			'creator_id' => 'required|integer',
			'author_id' => 'required|integer',

			'executors' => 'required|array',
			'executors.*' => 'required|integer',

			'controllers' => 'array',
			'controllers.*' => 'required|integer',

			'observers' => 'array',
			'observers.*' => 'required|integer',

			'user_id' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'document_id.required' => 'Идентификатор директивы не был передан!',
			'document_id.integer' => 'Идентификатор директивы должен быть целым числом!',

			'theme_id.required' => 'Идентификатор темы не был передан!',
			'theme_id.integer' => 'Идентификатор темы должен быть целым числом!',

			'executed_at.date' => 'Поле исполнено должно быть датой!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'portfolio.required' => 'Описание портфеля не было передано!',
			'portfolio.string' => 'Описание портфеля должно быть строкой!',

			'creator_id.required' => 'Идентификатор создателя не был передан!',
			'creator_id.integer' => 'Идентификатор создателя должен быть целым числом!',

			'author_id.required' => 'Идентификатор автора не был передан!',
			'author_id.integer' => 'Идентификатор автора должен быть целым числом!',

			'executors.required' => 'Идентификаторы исполнителей не были переданы!',
			'executors.*.integer' => 'Идентификатор исполнителя должен быть целым числом!',

			'controllers.*.integer' => 'Идентификатор контроллера должен быть целым числом!',

			'observers.*.integer' => 'Идентификатор наблюдателя должен быть целым числом!',

			'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',
		];
	}
}