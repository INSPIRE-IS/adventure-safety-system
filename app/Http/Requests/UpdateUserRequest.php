<?php

namespace App\Http\Requests;

use App\Models\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('user_edit');
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
            'surname' => [
                'string',
                'nullable',
            ],
            'mobile_number' => [
                'string',
                'nullable',
            ],
            'email' => [
                'required',
                'unique:users,email,' . request()->route('user')->id,
            ],
            'password' => [
                'required',
                'string',
            ],
            'roles' => [
                'required',
            ],
            // 'roles.*' => [
            //     'integer',
            // ],
            // 'roles' => [
            //     'required',
            //     'array',
            // ],
        ];
    }
}
