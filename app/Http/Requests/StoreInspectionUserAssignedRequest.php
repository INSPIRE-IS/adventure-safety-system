<?php

namespace App\Http\Requests;

use App\Models\InspectionUserAssigned;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreInspectionUserAssignedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('inspection_user_assigned_create');
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'inspection_id' => [
                'required',
                'integer',
            ],
            'assigned_to_id' => [
                'required',
                'integer',
            ],
            'due_by_date' => [
                'required',
                'date_format:' . config('panel.date_format'),
            ],
        ];
    }
}
