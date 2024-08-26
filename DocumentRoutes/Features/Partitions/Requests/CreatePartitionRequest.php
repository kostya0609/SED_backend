<?php

namespace SED\DocumentRoutes\Features\Partitions\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\Partitions\Dto\CreatePartitionDto;

class CreatePartitionRequest extends BaseRequest
{
    protected function getDtoClass(): ?string
    {
        return CreatePartitionDto::class;
    }

    public function createDto(): CreatePartitionDto
    {
        return parent::createDto();
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer',
            'title' => 'required|string',
            'user_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'parent_id.integer' => 'ID родительского раздела должен быть целым числом!',

            'title.required' => 'Название раздела не было передано!',
            'title.string' => 'Название раздела должно быть строкой!',

            'user_id.required' => 'ID пользователя не было передано!',
            'user_id.integer' => 'ID пользователя должно быть числом!',
        ];
    }
}
