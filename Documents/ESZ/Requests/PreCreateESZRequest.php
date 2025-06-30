<?php
namespace SED\Documents\ESZ\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\ESZ\Dto\PreCreateESZDto;

class PreCreateESZRequest extends BaseRequest
{
	public function createDto(): PreCreateESZDto
	{
		$dto = new PreCreateESZDto();
		$dto->content = $this->input('content');
		$dto->portfolio = $this->input('portfolio');
		$dto->user_id = $this->input('user_id');
		$dto->tmp_doc_id = $this->input('tmp_doc_id');
		$dto->theme_title = $this->input('theme_title');
		$dto->parent_document_id = $this->input('parent_document_id');
		$dto->document_hierarchy_id = $this->input('document_hierarchy_id');

        $dto->root_tmp_id = $this->input('root_tmp_id');


        $dto->setSignatory($this->input('signatory'));

		foreach ($this->input('receivers') as $receiver) {
			$dto->addReceiver($receiver);
		}

		foreach ($this->input('observers') as $observer) {
			$dto->addObserver($observer);
		}

		return $dto;
	}

	public function rules(): array
	{
		return [
			'content' => 'required|string',
			'portfolio' => 'nullable|string',

			'user_id' => 'required|integer',

			'tmp_doc_id' => 'required_without_all:theme_title|nullable|integer',
			'theme_title' => 'required_without_all:tmp_doc_id|nullable|string',

			'parent_document_id' => 'nullable|integer',
			'document_hierarchy_id' => 'nullable|integer',

			'signatory' => 'required|array',
			'signatory.user_id' => 'nullable|integer',
			'signatory.can_deletable' => 'required|boolean',
			'signatory.static_role_id' => 'nullable|integer',
			'signatory.dynamic_role_id' => 'nullable|integer',

			'receivers.*' => 'required|array',
			'receivers.*.user_id' => 'nullable|integer',
			'receivers.*.can_deletable' => 'required|boolean',
			'receivers.*.static_role_id' => 'nullable|integer',
			'receivers.*.dynamic_role_id' => 'nullable|integer',

			'observers.*' => 'required|array',
			'observers.*.user_id' => 'nullable|integer',
			'observers.*.can_deletable' => 'required|boolean',
			'observers.*.static_role_id' => 'nullable|integer',
			'observers.*.dynamic_role_id' => 'nullable|integer',
		];
	}

	public function messages(): array
	{
		return [
			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'portfolio.string' => 'Описание портфеля документов должно быть строкой!',

			'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',

			'tmp_doc_id.required_without_all' => 'Идентификатор шаблона документа и заголовок темы не были переданы!',
			'theme_title.required_without_all' => 'Тема и идентификатор шаблона документа не были переданы!',

			'parent_document_id.integer' => 'Идентификатор родительского общего документа должен быть целым числом!',
			'document_hierarchy_id.integer' => 'Идентификатор иерархии документов должен быть целым числом!',

			'signatory.required' => 'Данные подписанта не были переданы!',
			'signatory.array' => 'Данные подписанта должны быть массивом!',
			'signatory.user_id.integer' => 'Идентификатор подписанта должен быть целым числом!',
			'signatory.can_deletable.required' => 'Признак возможности удаления подписанта не был передан!',
			'signatory.can_deletable.boolean' => 'Признак возможности удаления подписанта должен быть булевым значением!',
			'signatory.static_role_id.integer' => 'Идентификатор статической роли подписанта должен быть целым числом!',
			'signatory.dynamic_role_id.integer' => 'Идентификатор динамической роли подписанта должен быть целым числом!',

			'receivers.*.required' => 'Данные одного получателя не были переданы!',
			'receivers.*.array' => 'Данные одного получателя должны быть массивом!',
			'receivers.*.user_id.integer' => 'Идентификатор получателя должен быть целым числом!',
			'receivers.*.can_deletable.required' => 'Признак возможности удаления получателя не был передан!',
			'receivers.*.can_deletable.boolean' => 'Признак возможности удаления получателя должен быть булевым значением!',
			'receivers.*.static_role_id.integer' => 'Идентификатор статической роли получателя должен быть целым числом!',
			'receivers.*.dynamic_role_id.integer' => 'Идентификатор динамической роли получателя должен быть целым числом!',

			'observers.*.required' => 'Данные одного наблюдателя не были переданы!',
			'observers.*.array' => 'Данные одного наблюдателя должны быть массивом!',
			'observers.*.user_id.integer' => 'Идентификатор наблюдателя должен быть целым числом!',
			'observers.*.can_deletable.required' => 'Признак возможности удаления наблюдателя не был передан!',
			'observers.*.can_deletable.boolean' => 'Признак возможности удаления наблюдателя должен быть булевым значением!',
			'observers.*.static_role_id.integer' => 'Идентификатор статической роли наблюдателя должен быть целым числом!',
			'observers.*.dynamic_role_id.integer' => 'Идентификатор динамической роли наблюдателя должен быть целым числом!',
		];
	}
}
