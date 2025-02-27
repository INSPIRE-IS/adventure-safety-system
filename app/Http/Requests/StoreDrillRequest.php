<?php

namespace App\Http\Requests;

use App\Models\Drill;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDrillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('drill_create');
        /** @disregard P1013 */
        return auth()->user()->can('drill_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'due_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'complete_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
            'drill_schedule_id' => [
                'required',
                'integer',
            ],
            'signature_file_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
