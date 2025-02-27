<?php

namespace App\Http\Requests;

use App\Models\EquipmentInspectionDetail;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreEquipmentInspectionDetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('equipment_inspection_detail_create');
        /** @disregard P1013 */
        return true; // auth()->user()->can('equipment_inspection_detail_create');
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
            'inspection_id' => [
                'required',
                'integer',
            ],
            'category_item_id' => [
                'required',
                'integer',
            ],
            'category_detail_id' => [
                'required',
                'integer',
            ],
        ];
    }
}
