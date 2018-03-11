<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployee extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'gender' => 'required',
            'department_id' => 'required',
            'category_id' => 'required',
            'location_id' => 'required',
            'cost_centre' => 'required',
            'cost_centre_desc' => 'required',
            'gl_accounts' => 'required',
            'gl_description' => 'required'
        ];
    }
}
