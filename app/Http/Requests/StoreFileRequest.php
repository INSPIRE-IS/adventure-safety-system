<?php

namespace App\Http\Requests;

use App\Models\File;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return Gate::allows('file_create');
        /** @disregard P1013 */
        return true; // auth()->user()->can('file_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            // 'site_id' => [
            //     'required',
            //     'integer',
            // ],
            // 'table_name' => [
            //     'string',
            //     'required',
            // ],
            // 'file_name' => [
            //     'string',
            //     'required',
            // ],
            // 'row' => [
            //     'string',
            //     'required',
            // ],
            // 'file_base64' => [
            //     'string',
            //     'required',
            // ],
        ];
    }
}
