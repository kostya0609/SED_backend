<?php
namespace SED\Documents\Directive\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Directive\Dto\CreateUpdateDirectiveDto;
use SED\Documents\Directive\Dto\UpdateDirectiveDto;

class UpdateDirectiveRequest extends BaseRequest
{

	public function createDto(): UpdateDirectiveDto
	{
		$dto = new UpdateDirectiveDto();

		$dto->document_id = $this->input('document_id');
		$dto->executed_at = $this->input('executed_at');
		$dto->content = $this->input('content');
		$dto->portfolio = $this->input('portfolio');

		$dto->setAuthor($this->input('author')['user_id'], $this->input('author')['can_deletable']);

		foreach ($this->input('executors') as $executor) {
			$dto->addExecutor($executor['user_id'], $executor['can_deletable']);
		}

		foreach ($this->input('controllers') as $controller) {
			$dto->addController($controller['user_id'], $controller['can_deletable']);
		}

		foreach ($this->input('observers') as $observer) {
			$dto->addObserver($observer['user_id'], $observer['can_deletable']);
		}

		return $dto;
	}

	public function rules(): array
	{
		return [
			'document_id' => 'required|integer',
			'executed_at' => 'required|date',
			'content' => 'required|string',
			'portfolio' => 'nullable|string',

			'author' => 'required|array',
			'author.user_id' => 'nullable|integer',
			'author.can_deletable' => 'required|boolean',

			'executors.*' => 'required|array',
			'executors.*.user_id' => 'nullable|integer',
			'executors.*.can_deletable' => 'required|boolean',

			'controllers.*' => 'required|array',
			'controllers.*.user_id' => 'nullable|integer',
			'controllers.*.can_deletable' => 'required|boolean',

			'observers.*' => 'required|array',
			'observers.*.user_id' => 'nullable|integer',
			'observers.*.can_deletable' => 'required|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'document_id.required' => 'Идентификатор документа не был передан!',
			'document_id.integer' => 'Идентификатор документа должен быть целым числом!',

			'executed_at.required' => 'Дата и время выполнения не были переданы!',
			'executed_at.date' => 'Дата и время выполнения должны быть датой!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'portfolio.string' => 'Описание портфеля документов должно быть строкой!',

			'author.required' => 'Автор не был передан!',
			'author.array' => 'Автор должен быть массивом!',
			'author.user_id.integer' => 'Идентификатор пользователя автора должен быть целым числом!',
			'author.can_deletable.boolean' => 'Поле can_deletable должно быть булево!',

			'executors.*.required' => 'Исполнитель не был передан!',
			'executors.*.array' => 'Исполнитель должен быть массивом!',
			'executors.*.user_id.integer' => 'Идентификатор пользователя исполнителя должен быть целым числом!',
			'executors.*.can_deletable.boolean' => 'Поле can_deletable должно быть булево!',

			'controllers.*.required' => 'Контролер не был передан!',
			'controllers.*.array' => 'Контролер должен быть массивом!',
			'controllers.*.user_id.integer' => 'Идентификатор пользователя контролера должен быть целым числом!',
			'controllers.*.can_deletable.boolean' => 'Поле can_deletable должно быть булево!',

			'observers.*.required' => 'Наблюдатель не был передан!',
			'observers.*.array' => 'Наблюдатель должен быть массивом!',
			'observers.*.user_id.integer' => 'Идентификатор пользователя наблюдателя должен быть целым числом!',
			'observers.*.can_deletable.boolean' => 'Поле can_deletable должно быть булево!',
		];
	}
}