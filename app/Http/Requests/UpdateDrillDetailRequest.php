<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDrillDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('drill_detail_edit');
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
            'instructor_id' => [
                'required',
                'integer',
            ],
            'complete_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'signature_file_id' => [
                'required',
                'integer',
            ],
            'competent' => [
                'required',
            ],
            'type_drill_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
