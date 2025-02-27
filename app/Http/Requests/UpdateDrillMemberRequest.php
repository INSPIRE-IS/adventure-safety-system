<?php

namespace App\Http\Requests;

use App\Models\DrillMember;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateDrillMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('drill_member_edit');
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
            'compitent' => [
                'required',
            ],
            'user_id' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
