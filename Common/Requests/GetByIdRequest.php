<?php
namespace SED\Common\Requests;

class GetByIdRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'document_id' => 'required|integer',
			'user_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'document_id.required' => 'Идентификатор не был передан!',
            'document_id.integer' => 'Идентификатор должен быть числом!',

			'user_id.required' => 'Идентификатор пользователя не был передан!',
            'user_id.integer' => 'Идентификатор пользователя должен быть числом!',
        ];
    }
}
