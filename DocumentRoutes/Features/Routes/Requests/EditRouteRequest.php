<?php

namespace SED\DocumentRoutes\Features\Routes\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\Routes\Dto\EditRouteDto;

class EditRouteRequest extends BaseRequest
{
    protected function getDtoClass(): ?string
    {
        return EditRouteDto::class;
    }

    public function createDto(): EditRouteDto
    {
        return parent::createDto();
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'title' => 'required|string',
            'direction_id' => 'required|integer',
            'group_id' => 'required|integer',
            'description' => 'nullable|string',
            'partition_id' => 'required|integer',
            'departments' => 'required|array',
            'is_active' => 'required|boolean',
            'user_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID маршрута не было передано!',
            'id.integer' => 'ID маршрута должен быть целым числом!',

            'title.required' => 'Название раздела не было передано!',
            'title.string' => 'Название раздела должно быть строкой!',

            'direction_id.required' => 'ID направления не было передано!',
            'direction_id.integer' => 'ID направления должен быть целым числом!',

            'group_id.required' => 'ID группы не было передано!',
            'group_id.integer' => 'ID группы должен быть целым числом!',

            'description.string' => 'Описание маршрута должно быть строкой!',

            'partition_id.required' => 'ID раздела не было передано!',
            'partition_id.integer' => 'ID  раздела должен быть целым числом!',

            'departments.required' => 'IDs департаментов не были передано!',
            'departments.array' => 'IDs  департаментов должны быть массивом!',

            'is_active.required' => 'Признак активности маршрута не было передано!',
            'is_active.boolean' => 'Признак активности маршрута должен быть булевым!',

            'user_id.required' => 'ID пользователя не было передано!',
            'user_id.integer' => 'ID пользователя должно быть числом!',
        ];
    }
}
