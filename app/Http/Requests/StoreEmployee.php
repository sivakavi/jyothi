<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Employee;

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
        // $employee = Employee::find($this->employee);
        
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    'name' => 'required',
                    'gender' => 'required',
                    'employee_id' => 'required|unique:employees',
                    'department_id' => 'required',
                    'category_id' => 'required',
                    'location_id' => 'required',
                    'cost_centre' => 'required',
                    'cost_centre_desc' => 'required',
                    'gl_accounts' => 'required',
                    'gl_description' => 'required'
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'name' => 'required',
                    'gender' => 'required',
                    'employee_id' => 'required|unique:employees,employee_id,'.$this->employee,
                    'department_id' => 'required',
                    'category_id' => 'required',
                    'location_id' => 'required',
                    'cost_centre' => 'required',
                    'cost_centre_desc' => 'required',
                    'gl_accounts' => 'required',
                    'gl_description' => 'required'
                ];
            }
            default:break;
        }
    }
}
