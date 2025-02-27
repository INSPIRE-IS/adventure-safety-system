<?php

namespace App\Http\Requests;

use App\Models\EquipmentInspectionDetail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class UpdateEquipmentInspectionDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @disregard P1013 */
        return auth()->user()->can('equipment_inspection_detail_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'num_units_checked' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'num_passed' => [
                'required',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
            'num_failed' => [
                'nullable',
                'integer',
                'min:-2147483648',
                'max:2147483647',
            ],
        ];
    }
}
