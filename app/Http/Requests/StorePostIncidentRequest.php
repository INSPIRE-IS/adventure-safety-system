<?php

namespace App\Http\Requests;

use App\Models\PostIncident;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StorePostIncidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('post_incident_create');
        /** @disregard P1013 */
        return auth()->user()->can('post_incident_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'post_incident_schedule_id' => [
                'required',
                'integer',
            ],
            'assigned_to_id' => [
                'required',
                'integer',
            ],
            'site_id' => [
                'required',
                'integer',
            ],
            'due_date' => [
                'required',
            ],
        ];
    }
}
