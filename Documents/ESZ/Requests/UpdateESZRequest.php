<?php
namespace SED\Documents\ESZ\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\ESZ\Dto\UpdateESZDto;

class UpdateESZRequest extends BaseRequest
{
	public function createDto(): UpdateESZDto
	{
		$dto = new UpdateESZDto();
		$dto->document_id = $this->input('document_id');
		$dto->content = $this->input('content');
		$dto->portfolio = $this->input('portfolio');

		$dto->setSignatory($this->input('signatory')['user_id'], $this->input('signatory')['can_deletable']);

		foreach ($this->input('receivers') as $receiver) {
			$dto->addReceiver($receiver['user_id'], $receiver['can_deletable']);
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

			'content' => 'required|string',
			'portfolio' => 'nullable|string',

			'user_id' => 'required|integer',

			'signatory' => 'required|array',
			'signatory.user_id' => 'nullable|integer',
			'signatory.can_deletable' => 'required|boolean',

			'receivers.*' => 'required|array',
			'receivers.*.user_id' => 'nullable|integer',
			'receivers.*.can_deletable' => 'required|boolean',

			'observers.*' => 'required|array',
			'observers.*.user_id' => 'nullable|integer',
			'observers.*.can_deletable' => 'required|boolean',
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

			'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',

			'signatory.required' => 'Роль подписанта не была передана!',
			'signatory.array' => 'Роль подписанта должна быть массивом!',
			'signatory.user_id.integer' => 'Идентификатор пользователя в роли подписанта должен быть целым числом!',
			'signatory.can_deletable.boolean' => 'Доступность удаления роли подписанта должна быть булевым значением!',

			'receivers.*.required' => 'Роль получателя не была передана!',
			'receivers.*.array' => 'Роль получателя должна быть массивом!',
			'receivers.*.user_id.integer' => 'Идентификатор пользователя в роли получателя должен быть целым числом!',
			'receivers.*.can_deletable.boolean' => 'Доступность удаления роли получателя должна быть булевым значением!',

			'observers.*.required' => 'Роль наблюдателя не была передана!',
			'observers.*.array' => 'Роль наблюдателя должна быть массивом!',
			'observers.*.user_id.integer' => 'Идентификатор пользователя в роли наблюдателя должен быть целым числом!',
			'observers.*.can_deletable.boolean' => 'Доступность удаления роли наблюдателя должна быть булевым значением!',
		];
	}
}