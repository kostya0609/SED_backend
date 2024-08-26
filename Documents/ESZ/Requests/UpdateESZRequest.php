<?php
namespace SED\Documents\ESZ\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\ESZ\Dto\CreateUpdateESZDto;

class UpdateESZRequest extends BaseRequest
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
			'document_id' => 'required|integer',

			'content' => 'required|string',
			'portfolio' => 'nullable|string',

			'signatory_id' => 'required|integer',

			'receivers' => 'array',
			'receivers.*' => 'required|integer',

			'observers' => 'array',
			'observers.*' => 'required|integer',

			'user_id' => 'required|integer',
		];
	}

	public function messages(): array
	{
		return [
			'document_id.required' => 'Идентификатор ЭСЗ не был передан!',
			'document_id.integer' => 'Идентификатор ЭСЗ должен быть целым числом!',

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
		];
	}
}