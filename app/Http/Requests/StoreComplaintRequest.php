<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreComplaintRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('incident_create');
        /** @disregard P1013 */
        return true; // auth()->user()->can('incident_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'site_id' => [
                'required',
                'integer',
            ],
            'user_id' => [
                'required',
                'integer',
            ],
            'complaint_date' => [
                'required',
                'date_format:' . config('panel.date_format') . ' ' . config('panel.time_format'),
            ],
            'description' => [
                'required',
            ],
            // 'signature_file_id' => [
            //     'required',
            //     'integer',
            // ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $payloadKeys = array_keys($this->all());

        Log::warning('StoreComplaintRequest validation failed', [
            'route' => $this->route()?->getName(),
            'path' => $this->path(),
            'method' => $this->method(),
            'content_type' => $this->header('Content-Type'),
            'expects_json' => $this->expectsJson(),
            'is_json' => $this->isJson(),
            'auth_user_id' => $this->user()?->id,
            'payload_keys' => $payloadKeys,
            'site_id' => $this->input('site_id'),
            'user_id' => $this->input('user_id'),
            'complaint_date' => $this->input('complaint_date'),
            'description_present' => $this->has('description'),
            'description_len' => strlen((string) $this->input('description', '')),
            'errors' => $validator->errors()->toArray(),
        ]);

        parent::failedValidation($validator);
    }
}
