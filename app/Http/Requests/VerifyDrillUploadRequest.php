<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyDrillUploadRequest extends FormRequest
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
            'instructor_id' => [
                'required',
                'integer',
            ],
            'expected_members_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'member_uuids' => [
                'nullable',
                'array',
            ],
            'member_uuids.*' => [
                'required_with:member_uuids',
            ],
        ];
    }
}

