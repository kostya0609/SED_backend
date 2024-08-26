<?php
namespace SED\Documents\ESZ\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\ESZ\Dto\CreateUpdateESZDto;

class CreateESZRequest extends BaseRequest
{
	protected function getDtoClass(): ?string
	{
		return CreateUpdateESZDto::class;
	}

	public function createDto(): CreateUpdateESZDto
	{
		return parent::createDto();
	}

	public function rules(): array
	{
		return [
			'theme_id' => 'required|integer',
			'content' => 'required|string',
			'portfolio' => 'nullable|string',
			'signatory_id' => 'required|integer',

			'receivers' => 'required|array',
			'receivers.*' => 'required|integer',

			'observers' => 'array',
			'observers.*' => 'required|integer',

			'user_id' => 'required|integer',

			'tmp_doc_id' => 'required_without_all:theme_title|nullable|integer',
			'theme_title' => 'required_without_all:tmp_doc_id|nullable|string',
		];
	}

	public function messages(): array
	{
		return [
			'theme_id.required' => 'Идентификатор темы не был передан!',
			'theme_id.integer' => 'Идентификатор темы должен быть целым числом!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'portfolio.string' => 'Описание портфеля документов должно быть строкой!',

			'signatory_id.required' => 'Идентификатор подписанта не был передан!',
			'signatory_id.integer' => 'Идентификатор подписанта должен быть целым числом!',

			'receivers.required' => 'Идентификаторы адресаты не были переданы!',
			'receivers.*.integer' => 'Идентификатор адресаты должен быть целым числом!',

			'observers.*.integer' => 'Идентификатор наблюдателя должен быть целым числом!',

			'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',

			'tmp_doc_id.required_without_all' => 'Идентификатор шаблона документа и заголовок темы не были переданы!',
			'theme_title.required_without_all' => 'Тема и идентификатор шаблона документа не были переданы!',
		];
	}
}