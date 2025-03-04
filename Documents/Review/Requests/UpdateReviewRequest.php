<?php
namespace SED\Documents\Review\Requests;

use SED\Common\Requests\BaseRequest;
use SED\Documents\Review\Dto\UpdateReviewDto;

class UpdateReviewRequest extends BaseRequest
{

	public function createDto(): UpdateReviewDto
	{
		$dto = new UpdateReviewDto();
		$dto->document_id = $this->input('document_id');
		$dto->content = $this->input('content');
		$dto->portfolio = $this->input('portfolio');
		
		foreach ($this->input('receivers') as $receiver) {
			$dto->addReceiver($receiver['user_id'], $receiver['can_deletable']);
		}

		return $dto;
		
	}

	public function rules(): array
	{
		return [
			'document_id' => 'required|integer',

			'content' => 'required|string',

			'user_id' => 'required|integer',

            'receivers.*' => 'required|array',
			'receivers.*.user_id' => 'nullable|integer',
			'receivers.*.can_deletable' => 'required|boolean',
		];
	}

	public function messages(): array
	{
		return [
			'document_id.required' => 'Идентификатор ознакомления не был передан!',
			'document_id.integer' => 'Идентификатор ознакомления должен быть целым числом!',

			'content.required' => 'Содержание не было передано!',
			'content.string' => 'Содержание должно быть строкой!',

			'portfolio.string' => 'Описание портфеля документов должно быть строкой!',

            'user_id.required' => 'Идентификатор пользователя не был передан!',
			'user_id.integer' => 'Идентификатор пользователя должен быть целым числом!',

            'receivers.*.required' => 'Роль получателя не была передана!',
			'receivers.*.array' => 'Роль получателя должна быть массивом!',
			'receivers.*.user_id.integer' => 'Идентификатор пользователя в роли получателя должен быть целым числом!',
			'receivers.*.can_deletable.boolean' => 'Доступность удаления роли получателя должна быть булевым значением!',
        ];
	}
}
