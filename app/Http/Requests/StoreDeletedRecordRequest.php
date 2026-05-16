<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreDeletedRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => ['required', 'integer'],
            'module' => ['required', 'string', 'max:50'],
            'record_id' => ['nullable', 'integer'],
            'detail_id' => ['nullable', 'integer'],
            'file_count' => ['nullable', 'integer', 'min:0'],
            'deleted_at' => ['nullable', 'date'],
            'payload' => ['required', 'array'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (config('app.mobile_upload_debug')) {
            Log::warning('StoreDeletedRecordRequest validation failed', [
                'route' => $this->route()?->getName(),
                'path' => $this->path(),
                'method' => $this->method(),
                'content_type' => $this->header('Content-Type'),
                'expects_json' => $this->expectsJson(),
                'auth_user_id' => $this->user()?->id,
                'payload_keys' => array_keys($this->all()),
                'team_id' => $this->input('team_id'),
                'module' => $this->input('module'),
                'record_id' => $this->input('record_id'),
                'detail_id' => $this->input('detail_id'),
                'file_count' => $this->input('file_count'),
                'deleted_at' => $this->input('deleted_at'),
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        parent::failedValidation($validator);
    }
}