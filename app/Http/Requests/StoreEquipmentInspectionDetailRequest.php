<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreEquipmentInspectionDetailRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return true;
    }

    public function rules()
    {
        return [
            'site_id' => [
                'required',
                'integer',
            ],
            'inspection_id' => [
                'required',
                'integer',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (config('app.mobile_upload_debug')) {
            Log::warning('StoreEquipmentInspectionDetailRequest validation failed', [
                'route' => $this->route()?->getName(),
                'path' => $this->path(),
                'method' => $this->method(),
                'content_type' => $this->header('Content-Type'),
                'auth_user_id' => $this->user()?->id,
                'payload_keys' => array_keys($this->all()),
                'site_id' => $this->input('site_id'),
                'inspection_id' => $this->input('inspection_id'),
                'category_item_id' => $this->input('category_item_id'),
                'category_detail_id' => $this->input('category_detail_id'),
                'identity' => $this->input('id'),
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        parent::failedValidation($validator);
    }
}