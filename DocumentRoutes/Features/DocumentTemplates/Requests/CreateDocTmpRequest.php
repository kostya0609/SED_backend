<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Requests;

use SED\Common\Requests\BaseRequest;
use SED\DocumentRoutes\Features\DocumentTemplates\Dto\CreateDocTmpDto;

class CreateDocTmpRequest extends BaseRequest
{
    protected function getDtoClass(): ?string
    {
        return CreateDocTmpDto::class;
    }

    public function createDto(): CreateDocTmpDto
    {
        return parent::createDto();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'parent_id' => 'nullable|integer',
            'route_id' => 'required|integer',
            'type_id' => 'required|integer',            
            'data' => 'required|array',
            'requirements' => 'nullable|string',
            'is_start' =>'required|boolean',            
            'is_active' =>'required|boolean',            
            'user_id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Название раздела не было передано!',
            'title.string' => 'Название раздела должно быть строкой!',

            'parent_id.integer' => 'ID родительского шаблона должен быть целым числом!',

            'route_id.required' => 'ID маршрута не было передано!',
            'route_id.integer' => 'ID маршрута должен быть целым числом!',

            'type_id.required' => 'ID типа документа не было передано!',
            'type_id.integer' => 'ID типа документа должен быть целым числом!',
           
            'data.required' => 'Данные документа в JSON не были передано!',
            'data.array' => 'Данные документа в JSON должны быть массивом!',

            'requirements.string' => 'Требования документа должны быть строкой!',

            'is_start.required' => 'Признак начального документа не было передано!',
            'is_start.boolean' => 'Признак начального документа должен быть булевым!',

            'is_active.required' => 'Признак активного документа не было передано!',
            'is_active.boolean' => 'Признак активного документа должен быть булевым!',

            'user_id.required' => 'ID пользователя не было передано!',
            'user_id.integer' => 'ID пользователя должно быть числом!',
        ];
    }
}
