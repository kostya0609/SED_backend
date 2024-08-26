<?php

namespace SED\DocumentRoutes\Features\DocumentTemplates\Requests;

use SED\Common\Requests\BaseRequest;

class GetDeleteDeactivateDocTmpRequest extends BaseRequest
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
            'id.required' => 'ID шаблона документа не было передано!',
            'id.integer' => 'ID шаблона документа должно быть числом!',
        ];
    }
}
