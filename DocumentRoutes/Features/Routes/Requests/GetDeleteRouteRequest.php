<?php

namespace SED\DocumentRoutes\Features\Routes\Requests;

use SED\Common\Requests\BaseRequest;

class GetDeleteRouteRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'ID маршрута не было передано!',
            'id.integer' => 'ID маршрута должен быть целым числом!',
        ];
    }
}
