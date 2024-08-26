<?php
namespace SED\Documents\Directive\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Directive\Dto\CreateUpdateDirectiveDto;

class CreateDirectiveRequest extends BaseRequest
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
			'executed_at' => 'required|date',

			'content' => 'required|string',

			'creator_id' => 'required|integer',
			'author_id' => 'required|integer',

			'executors' => 'required|array',
			'executors.*' => 'required|integer',

			'controllers' => 'array',
			'controllers.*' => 'required|integer',

			'observers' => 'array',
			'observers.*' => 'required|integer',

			'tmp_doc_id' => 'required_without_all:theme_title|nullable|integer',
			'theme_title' => 'required_without_all:tmp_doc_id|nullable|string',
		];
	}

	public function messages(): array
	{
		return [
			'executed_at.date' => 'Поле исполнено должно быть датой!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'creator_id.required' => 'Идентификатор создателя не был передан!',
			'creator_id.integer' => 'Идентификатор создателя должен быть целым числом!',

			'author_id.required' => 'Идентификатор автора не был передан!',
			'author_id.integer' => 'Идентификатор автора должен быть целым числом!',

			'executors.required' => 'Идентификаторы исполнителей не были переданы!',
			'executors.*.integer' => 'Идентификатор исполнителя должен быть целым числом!',

			'controllers.*.integer' => 'Идентификатор контроллера должен быть целым числом!',

			'observers.*.integer' => 'Идентификатор наблюдателя должен быть целым числом!',

			'tmp_doc_id.integer' => 'Идентификатор шаблона документа должен быть целым числом!',
			'theme_title.string' => 'Тема должна быть строкой!',

			'tmp_doc_id.required_without_all' => 'Идентификатор шаблона документа и заголовок темы не были переданы!',
            'theme_title.required_without_all' => 'Тема и идентификатор шаблона документа не были переданы!',
		];
	}
}