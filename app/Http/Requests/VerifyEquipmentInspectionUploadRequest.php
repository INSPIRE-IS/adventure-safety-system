<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEquipmentInspectionUploadRequest extends FormRequest
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
            'detail_keys' => [
                'nullable',
                'array',
            ],
            'detail_keys.*.identity' => [
                'required_with:detail_keys',
            ],
            'detail_keys.*.category_item_id' => [
                'required_with:detail_keys',
                'integer',
            ],
            'detail_keys.*.category_detail_id' => [
                'required_with:detail_keys',
                'integer',
            ],
        ];
    }
}

