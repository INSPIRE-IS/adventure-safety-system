<?php

namespace App\Http\Requests;

use App\Models\TypeActivity;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTypeActivityRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('type_activity_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'string',
                'required',
            ],
        ];
    }
}
