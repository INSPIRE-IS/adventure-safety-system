<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyInspectionUploadRequest extends FormRequest
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
            'expected_details_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'expected_files_count' => [
                'nullable',
                'integer',
                'min:0',
            ],
            'detail_keys' => [
                'nullable',
                'array',
            ],
            'detail_keys.*.category_item_id' => [
                'required_with:detail_keys',
                'integer',
            ],
            'detail_keys.*.category_detail_id' => [
                'required_with:detail_keys',
                'integer',
            ],
            'file_keys' => [
                'nullable',
                'array',
            ],
            'file_keys.*.category_item_id' => [
                'required_with:file_keys',
                'integer',
            ],
            'file_keys.*.category_detail_id' => [
                'required_with:file_keys',
                'integer',
            ],
            'file_keys.*.type_file_id' => [
                'required_with:file_keys',
                'integer',
            ],
        ];
    }
}

