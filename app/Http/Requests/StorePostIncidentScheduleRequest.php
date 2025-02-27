<?php

namespace App\Http\Requests;

use App\Models\PostIncidentSchedule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePostIncidentScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('post_incident_schedule_create');
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
                'required',
            ],
            'insident_id' => [
                'required',
                'integer',
            ],
            'assigned_by_id' => [
                'required',
                'integer',
            ],
            'assigned_to_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
