<?php

namespace SED\DocumentRoutes\Features\Partitions\Requests;

use SED\Common\Requests\BaseRequest;

class GetDeletePartitionRequest extends BaseRequest
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
            'id.required' => 'ID раздела не был передан!',
            'id.integer' => 'ID раздела должен быть целым числом!',
        ];
    }
}
