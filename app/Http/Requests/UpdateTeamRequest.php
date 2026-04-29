<?php

namespace App\Http\Requests;

use App\Models\Team;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('site_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'location' => [
                'string',
                'nullable',
            ],
            'color_primary' => [
                'string',
                'nullable',
            ],
            'color_secondary' => [
                'string',
                'nullable',
            ],
            'color_other' => [
                'string',
                'nullable',
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
            //'image' => 'nullable|image|max:10240', // Ensure the file is an image and <10MB
            'type_activities' => [
                'required',
                'array',
                'min:1',
            ],
            'type_activities.*' => [
                'integer',
                'exists:type_activities,id',
            ],
        ];
    }
}
