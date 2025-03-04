<?php
namespace SED\Documents\Directive\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Directive\Dto\PreCreateDirectiveDto;

class PreCreateDirectiveRequest extends BaseRequest
{
	public function createDto(): PreCreateDirectiveDto
	{
		$dto = new PreCreateDirectiveDto();

		$dto->executed_at = $this->input('executed_at');
		$dto->content = $this->input('content');
		$dto->portfolio = $this->input('portfolio');
		$dto->creator_id = $this->input('creator_id');
		$dto->tmp_doc_id = $this->input('tmp_doc_id');
		$dto->theme_title = $this->input('theme_title');
		$dto->parent_document_id = $this->input('parent_document_id');

		$dto->setAuthor($this->input('author'));

		foreach ($this->input('executors') as $executor) {
			$dto->addExecutor($executor);
		}

		foreach ($this->input('controllers') as $controller) {
			$dto->addController($controller);
		}

		foreach ($this->input('observers') as $observer) {
			$dto->addObserver($observer);
		}

		return $dto;
	}

	public function rules(): array
	{
		return [
			'executed_at' => 'required|date',
			'content' => 'required|string',
			'portfolio' => 'nullable|string',

			'creator_id' => 'required|integer',

			'tmp_doc_id' => 'required_without_all:theme_title|nullable|integer',
			'theme_title' => 'required_without_all:tmp_doc_id|nullable|string',

			'parent_document_id' => 'nullable|integer',

			'author' => 'required|array',
            'author.user_id' => 'nullable|integer',
            'author.can_deletable' => 'required|boolean',
            'author.static_role_id' => 'nullable|integer',
            'author.dynamic_role_id' => 'nullable|integer',

			'executors.*' => 'required|array',
            'executors.*.user_id' => 'nullable|integer',
            'executors.*.can_deletable' => 'required|boolean',
            'executors.*.static_role_id' => 'nullable|integer',
            'executors.*.dynamic_role_id' => 'nullable|integer',

			'controllers.*' => 'required|array',
            'controllers.*.user_id' => 'nullable|integer',
            'controllers.*.can_deletable' => 'required|boolean',
            'controllers.*.static_role_id' => 'nullable|integer',
            'controllers.*.dynamic_role_id' => 'nullable|integer',

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
			'tmp_doc_id.required_without_all' => 'Идентификатор шаблона документа и заголовок темы не были переданы!',
			'theme_title.required_without_all' => 'Тема и идентификатор шаблона документа не были переданы!',
			'executed_at.required' => 'Дата и время исполнения не было передано!',
            'executed_at.date' => 'Дата и время исполнения должно быть датой!',
            'content.required' => 'Содержание документа не было передано!',
            'content.string' => 'Содержание документа должно быть строкой!',
			'portfolio.string' => 'Описание портфеля документов должно быть строкой!',
            
			'creator_id.required' => 'Идентификатор создателя не был передан!',
            'creator_id.integer' => 'Идентификатор создателя должен быть целым числом!',
			
			'author.required' => 'Автор не был передан!',
            'author.array' => 'Автор должен быть массивом!',
            'author.*.user_id' => 'Идентификатор автора должен быть целым числом!',
			'author.*.can_deletable' => 'Права удаления автора должны быть булевыми!',
            'author.*.static_role_id' => 'Идентификатор статической роли автора должен быть целым числом!',
			'author.*.dynamic_role_id' => 'Идентификатор динамической роли автора должен быть целым числом!',

            'executors.*.required' => 'Исполнитель не был передан!',
            'executors.*.array' => 'Исполнитель должен быть массивом!',
			'executors.*.user_id' => 'Идентификатор исполнителя должен быть целым числом!',
            'executors.*.can_deletable' => 'Права удаления исполнителя должны быть булевыми!',
			'executors.*.static_role_id' => 'Идентификатор статической роли исполнителя должен быть целым числом!',
            'executors.*.dynamic_role_id' => 'Идентификатор динамической роли исполнителя должен быть целым числом!',

			'controllers.*.required' => 'Контроллер не был передан!',
            'controllers.*.array' => 'Контроллер должен быть массивом!',
            'controllers.*.user_id' => 'Идентификатор контроллера должен быть целым числом!',
			'controllers.*.can_deletable' => 'Права удаления контроллера должны быть булевыми!',
            'controllers.*.static_role_id' => 'Идентификатор статической роли контроллера должен быть целым числом!',
			'controllers.*.dynamic_role_id' => 'Идентификатор динамической роли контроллера должен быть целым числом!',

			'observers.*.required' => 'Наблюдатель не был передан!',
            'observers.*.array' => 'Наблюдатель должен быть массивом!',
            'observers.*.user_id' => 'Идентификатор наблюдателя должен быть целым числом!',
			'observers.*.can_deletable' => 'Права удаления наблюдателя должны быть булевыми!',
            'observers.*.static_role_id' => 'Идентификатор статической роли наблюдателя должен быть целым числом!',
			'observers.*.dynamic_role_id' => 'Идентификатор динамической роли наблюдателя должен быть целым числом!',

			'parent_document_id.integer' => 'Идентификатор родительского документа должен быть целым числом!',
		];
	}
}