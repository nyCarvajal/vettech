<?php

namespace App\Http\Requests;

class UpdateProcedureRequest extends StoreProcedureRequest
{
    public function rules(): array
    {
        return parent::rules();
    }
}
