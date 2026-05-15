<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreInspectionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
            'inspection_schedule_id' => [
                'required',
                'integer',
            ],
            'due_date' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
            'complete_date' => [
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
                'nullable',
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if (config('app.mobile_upload_debug')) {
            Log::warning('StoreInspectionRequest validation failed', [
                'route' => $this->route()?->getName(),
                'path' => $this->path(),
                'method' => $this->method(),
                'content_type' => $this->header('Content-Type'),
                'expects_json' => $this->expectsJson(),
                'auth_user_id' => $this->user()?->id,
                'payload_keys' => array_keys($this->all()),
                'site_id' => $this->input('site_id'),
                'id' => $this->input('id'),
                'inspection_schedule_id' => $this->input('inspection_schedule_id'),
                'due_date' => $this->input('due_date'),
                'complete_date' => $this->input('complete_date'),
                'closing_comment_len' => strlen((string) $this->input('closing_comment', '')),
                'errors' => $validator->errors()->toArray(),
            ]);
        }

        parent::failedValidation($validator);
    }
}