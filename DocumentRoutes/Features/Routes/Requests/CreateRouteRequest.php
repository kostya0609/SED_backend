<?php

namespace SED\DocumentRoutes\Features\Routes\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\Routes\Dto\CreateRouteDto;

class CreateRouteRequest extends BaseRequest
{
    protected function getDtoClass(): ?string
    {
        return CreateRouteDto::class;
    }

    public function createDto(): CreateRouteDto
    {
        return parent::createDto();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'direction_id' => 'required|integer',
            'group_id' => 'required|integer',
            'description' => 'nullable|string',
            'partition_id' => 'required|integer',
            'departments' => 'required|array',
            'user_id' => 'required|integer',
            'is_active' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название раздела не было передано!',
            'title.string' => 'Название раздела должно быть строкой!',

            'direction_id.required' => 'ID направления не было передано!',
            'direction_id.integer' => 'ID направления должен быть целым числом!',

            'group_id.required' => 'ID группы не было передано!',
            'group_id.integer' => 'ID группы должен быть целым числом!',

            'description.string' => 'Описание маршрута должно быть строкой!',

            'partition_id.required' => 'ID раздела не было передано!',
            'partition_id.integer' => 'ID  раздела должен быть целым числом!',

            'departments.required' => 'ID департаментов не были передано!',
            'departments.array' => 'IDs  департаментов должны быть массивом!',

            'user_id.required' => 'ID пользователя не было передано!',
            'user_id.integer' => 'ID пользователя должно быть числом!',

            'is_active.required' => 'Поле активности не было передано!',
            'is_active.boolean' => 'Поле активности должно быть булевым значением!',
        ];
    }
}
