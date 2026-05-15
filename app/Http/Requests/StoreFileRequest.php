<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('file_create');
        /** @disregard P1013 */
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'file' => [
                'required',
                'file',
                'max:10240', // 10MB
                'mimes:jpeg,png,jpg,pdf',
            ],
            'file_name' => [
                'string',
                'required',
            ],
            'file_type' => [
                'string',
            ],
            'type_file_id' => [
                'required',
            ],
            'table_name' => [
                'string',
            ],
            'table_row_id' => [
                'integer',
            ],
            'site_id' => [
                'required',
                'integer',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (config('app.mobile_upload_debug')) {
            $uploadedFile = $this->file('file');

            Log::warning('StoreFileRequest validation failed', [
                'route' => $this->route()?->getName(),
                'path' => $this->path(),
                'method' => $this->method(),
                'content_type' => $this->header('Content-Type'),
                'expects_json' => $this->expectsJson(),
                'auth_user_id' => $this->user()?->id,
                'payload_keys' => array_keys($this->all()),
                'site_id' => $this->input('site_id'),
                'table_name' => $this->input('table_name'),
                'table_row_id' => $this->input('table_row_id'),
                'type_file_id' => $this->input('type_file_id'),
                'file_name' => $this->input('file_name'),
                'file_type' => $this->input('file_type'),
                'upload_size' => $uploadedFile?->getSize(),
                'upload_mime' => $uploadedFile?->getMimeType(),
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        parent::failedValidation($validator);
    }
}