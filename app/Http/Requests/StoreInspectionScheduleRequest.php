<?php

namespace App\Http\Requests;

use App\Models\InspectionSchedule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInspectionScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('inspection_schedule_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => [
                'string',
                'nullable',
            ],
            'site_id' => [
                'required',
                'integer',
            ],
            'type_inspection_id' => [
                'required',
                'integer',
            ],
            'active_date' => [
                'required',
                //'date_format:' . config('panel.date_format'),
            ],
            'category_items.*' => [
                'integer',
            ],
            'category_items' => [
                'required',
                'array',
            ],
        ];
    }
}
