<?php

namespace SED\DocumentRoutes\Features\Partitions\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\Partitions\Dto\EditPartitionDto;

class EditPartitionRequest extends BaseRequest
{
    protected function getDtoClass(): ?string
    {
        return EditPartitionDto::class;
    }

    public function createDto(): EditPartitionDto
    {
        return parent::createDto();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'user_id' => 'required|integer',
            'parent_id' => 'nullable|integer',
            'title' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID раздела не был передан!',
            'id.integer' => 'ID раздела должен быть целым числом!',

            'parent_id.integer' => 'ID родительского раздела должен быть целым числом!',

            'title.required' => 'Название раздела не было передано!',
            'title.string' => 'Название раздела должно быть строкой!',

            'user_id.required' => 'ID пользователя не было передано!',
            'user_id.integer' => 'ID пользователя должно быть числом!',
        ];
    }
}
