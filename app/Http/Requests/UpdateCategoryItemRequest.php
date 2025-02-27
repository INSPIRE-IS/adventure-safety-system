<?php

namespace App\Http\Requests;

use App\Models\CategoryItem;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateCategoryItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('category_item_edit');
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
            'in_use_date' => [
                'string',
                'nullable',
            ],
            'out_use_date' => [
                'string',
                'nullable',
            ],
            'certificate_recieved_id' => [
                'required',
            ],
        ];
    }
}
