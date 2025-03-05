<?php

namespace App\Http\Requests;

use App\Models\DrillSchedule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDrillScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('drill_schedule_create');
        /** @disregard P1013 */
        return true; // auth()->user()->can('drill_schedule_create');
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
            'type_drill_id' => [
                'required',
                'integer',
            ],
            'period_id' => [
                'required',
            ],
            'active_date' => [
                'required',
                // 'date_format:' . config('panel.date_format'),
            ],
            'assigned_to_id' => [
                'required',
                'integer',
            ],
            'type_activity_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
