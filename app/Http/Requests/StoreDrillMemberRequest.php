<?php

namespace App\Http\Requests;

use App\Models\DrillMember;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreDrillMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return Gate::allows('drill_member_create');
        /** @disregard P1013 */
        return auth()->user()->can('drill_member_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'site_id' => [
                'required',
                'integer',
            ],
            'drill_detail_id' => [
                'required',
                'integer',
            ],
            'member_name' => [
                'string',
                'nullable',
            ],
            'user_id' => [
                'nullable',
                'integer',
            ],
            'compitent' => [
                'required',
            ],

        ];
    }
}
