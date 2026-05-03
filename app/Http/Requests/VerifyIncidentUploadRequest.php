<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyIncidentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => [
                'nullable',
                'integer',
            ],
            'uuid' => [
                'required',
            ],
            'expected_files_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }
}

