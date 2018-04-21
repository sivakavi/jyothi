<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Employee;
use App\EmployeeLog;
use App\Category;
use App\Location;
use App\Department;
use App\Http\Requests\StoreEmployee;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;



class EmployeeController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$employees = Employee::withoutGlobalScopes()->orderBy('id', 'asc')->paginate(10);

		return view('admin.employees.index', compact('employees'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$departments = Department::all(['id', 'name']);
		$categories = Category::all(['id', 'name']);
		$locations = Location::all(['id', 'name']);

		return view('admin.employees.create', compact('departments','categories','locations'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreEmployee $request)
	{
		$employee = new Employee();

		$employee->name = $request->input("name");
		$employee->gender = $request->input("gender");
		$employee->employee_id = $request->input("employee_id");
		$employee->department_id = $request->input("department_id");
		$employee->category_id = $request->input("category_id");
		$employee->location_id = $request->input("location_id");
		$employee->cost_centre = $request->input("cost_centre");
		$employee->cost_centre_desc = $request->input("cost_centre_desc");
		$employee->gl_accounts = $request->input("gl_accounts");
		$employee->gl_description = $request->input("gl_description");
		$employee->title = $request->input("title");
		$employee->marital_status = $request->input("marital_status");
		$employee->position_desc = $request->input("position_desc");

		$employee->perm_address = $request->input("perm_address");
		$employee->perm_city = $request->input("perm_city");
		$employee->perm_district = $request->input("perm_district");
		$employee->perm_state = $request->input("perm_state");
		$employee->perm_country = $request->input("perm_country");
		$employee->perm_pincode = $request->input("perm_pincode");

		$employee->present_address = $request->input("present_address");
		$employee->present_city = $request->input("present_city");
		$employee->present_district = $request->input("present_district");
		$employee->present_state = $request->input("present_state");
		$employee->present_country = $request->input("present_country");
		$employee->present_pincode = $request->input("present_pincode");

		$employee->official_email = $request->input("official_email");
		$employee->personal_mobile_no = $request->input("personal_mobile_no");
		$employee->personal_email_id = $request->input("personal_email_id");
		$employee->dob = $request->input("dob");
		$employee->doj = $request->input("doj");
		$employee->doc = $request->input("doc");

		$employee->pan_no = $request->input("pan_no");
		$employee->aadhar_no = $request->input("aadhar_no");
		$employee->pf_no = $request->input("pf_no");
		$employee->uan_no = $request->input("uan_no");
		$employee->esic_no = $request->input("esic_no");
		$employee->qualification = $request->input("qualification");

		$employee->spouse_name = $request->input("spouse_name");
		$employee->spouse_dob = $request->input("spouse_dob");
		$employee->father_name = $request->input("father_name");
		$employee->father_dob = $request->input("father_dob");
		$employee->mother_name = $request->input("mother_name");
		$employee->mother_dob = $request->input("mother_dob");

		$employee->child1_name = $request->input("child1_name");
		$employee->child1_dob = $request->input("child1_dob");
		$employee->child2_name = $request->input("child2_name");
		$employee->child2_dob = $request->input("child2_dob");

		$employee->blood_group = $request->input("blood_group");
		$employee->reporting_manager = $request->input("reporting_manager");
		$employee->remark = $request->input("remark");

		$employee->save();

		return redirect()->route('admin.employees.index')->with('message', 'Employee created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$employee = Employee::findOrFail($id);

		return view('admin.employees.show', compact('employee'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$employee = Employee::withoutGlobalScopes()->findOrFail($id);
		// dd($employee);
		$departments = Department::all(['id', 'name']);
		$categories = Category::all(['id', 'name']);
		$locations = Location::all(['id', 'name']);

		return view('admin.employees.edit', compact('employee', 'departments', 'categories', 'locations'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreEmployee $request, $id)
	{
		$employee = Employee::withoutGlobalScopes()->findOrFail($id);

		$employee->name = $request->input("name");
		$employee->gender = $request->input("gender");
		$employee->employee_id = $request->input("employee_id");
		$employee->department_id = $request->input("department_id");
		$employee->category_id = $request->input("category_id");
		$employee->location_id = $request->input("location_id");
		$employee->cost_centre = $request->input("cost_centre");
		$employee->cost_centre_desc = $request->input("cost_centre_desc");
		$employee->gl_accounts = $request->input("gl_accounts");
		$employee->gl_description = $request->input("gl_description");
		$employee->title = $request->input("title");
		$employee->marital_status = $request->input("marital_status");
		$employee->position_desc = $request->input("position_desc");

		$employee->perm_address = $request->input("perm_address");
		$employee->perm_city = $request->input("perm_city");
		$employee->perm_district = $request->input("perm_district");
		$employee->perm_state = $request->input("perm_state");
		$employee->perm_country = $request->input("perm_country");
		$employee->perm_pincode = $request->input("perm_pincode");

		$employee->present_address = $request->input("present_address");
		$employee->present_city = $request->input("present_city");
		$employee->present_district = $request->input("present_district");
		$employee->present_state = $request->input("present_state");
		$employee->present_country = $request->input("present_country");
		$employee->present_pincode = $request->input("present_pincode");

		$employee->official_email = $request->input("official_email");
		$employee->personal_mobile_no = $request->input("personal_mobile_no");
		$employee->personal_email_id = $request->input("personal_email_id");
		$employee->dob = $request->input("dob");
		$employee->doj = $request->input("doj");
		$employee->doc = $request->input("doc");

		$employee->pan_no = $request->input("pan_no");
		$employee->aadhar_no = $request->input("aadhar_no");
		$employee->pf_no = $request->input("pf_no");
		$employee->uan_no = $request->input("uan_no");
		$employee->esic_no = $request->input("esic_no");
		$employee->qualification = $request->input("qualification");

		$employee->spouse_name = $request->input("spouse_name");
		$employee->spouse_dob = $request->input("spouse_dob");
		$employee->father_name = $request->input("father_name");
		$employee->father_dob = $request->input("father_dob");
		$employee->mother_name = $request->input("mother_name");
		$employee->mother_dob = $request->input("mother_dob");

		$employee->child1_name = $request->input("child1_name");
		$employee->child1_dob = $request->input("child1_dob");
		$employee->child2_name = $request->input("child2_name");
		$employee->child2_dob = $request->input("child2_dob");

		$employee->blood_group = $request->input("blood_group");
		$employee->reporting_manager = $request->input("reporting_manager");
		$employee->remark = $request->input("remark");


		$employee->save();

		return redirect()->route('admin.employees.index')->with('message', 'Employee updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$employee = Employee::findOrFail($id);
		$employee->delete();

		return redirect()->route('admin.employees.index')->with('message', 'Employee deleted successfully.');
	}

	public function importExcel(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|mimes:xlsx'
        ]);
        
        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
		if ($request->hasFile('import_file')) {
			$departments = $this->formatData(Department::all(['id', 'name']));
			$categories = $this->formatData(Category::all(['id', 'name']));
			$locations = $this->formatData(Location::all(['id', 'name']));
			
			$path = $request->file('import_file')->getRealPath();
			$rows = \Excel::load($path, function($reader) {
			})->toArray();
			$employees = [];
			$existEmployees = [];
			$employeeIds = [];
			foreach($rows as $row){
				if($row['emp._no.'] != ''){
					// dd($row);
					$exist = $this->checkEmployeeID($row['emp._no.']);
					if(!$exist){
						$employee['name'] = $row['name'];
						$employee['gender'] = strtolower($row['gender']);
						$employee['employee_id'] = $row['emp._no.'];
						$employeeIds[] = $employee['employee_id'];
						$employee['department_id'] = $departments[strtolower($row['department'])];
						$employee['category_id'] = $categories[strtolower($row['category'])];
						$employee['location_id'] = $locations[strtolower($row['location'])];
						$employee['cost_centre'] = $row['cost_centre'];
						$employee['cost_centre_desc'] = $row['cost_centre_desc'];
						$employee['gl_accounts'] = $row['gl_accounts'];
						$employee['gl_description'] = $row['gl_discription'];
						$employee['title'] = $row['title'];
						$employee['marital_status'] = $row['marital_status'];
						$employee['position_desc'] = $row['position_desc.'];
						$employee['perm_address'] = $row['perm._address'];
						$employee['perm_city'] = $row['perm._city'];
						$employee['perm_district'] = $row['perm._district'];
						$employee['perm_state'] = $row['perm._state'];
						$employee['perm_country'] = $row['perm._country'];
						$employee['perm_pincode'] = $row['perm._pin_code'];
						$employee['present_address'] = $row['prsnt._address'];
						$employee['present_city'] = $row['prsnt._city'];
						$employee['present_district'] = $row['prsnt._district'];
						$employee['present_state'] = $row['prsnt._state'];
						$employee['present_country'] = $row['prsnt._country'];
						$employee['present_pincode'] = $row['prsnt._pin_code'];
						$employee['official_email'] = $row['official_email_id'];
						$employee['personal_mobile_no'] = $row['personal_mobile_no'];
						$employee['personal_email_id'] = $row['personal_email_id'];
						$employee['dob'] = $row['dob'];
						$employee['doj'] = $row['doj'];
						$employee['doc'] = $row['confirmation_date'];
						$employee['pan_no'] = $row['pan_no.'];
						$employee['aadhar_no'] = $row['aadhar_no.'];
						$employee['pf_no'] = $row['pf_no.'];
						$employee['uan_no'] = $row['uan_no.'];
						$employee['esic_no'] = $row['esic_no.'];
						$employee['qualification'] = $row['qualification'];
						$employee['spouse_name'] = $row['spouse_name'];
						$employee['spouse_dob'] = $row['spouse_dob'];
						$employee['father_name'] = $row['father_name'];
						$employee['father_dob'] = $row['father_dob'];
						$employee['mother_name'] = $row['mother_name'];
						$employee['mother_dob'] = $row['mother_dob'];
						$employee['child1_name'] = $row['child_name_1'];
						$employee['child1_dob'] = $row['child_dob_1'];
						$employee['child2_name'] = $row['child_name_2'];
						$employee['child2_dob'] = $row['child_dob_2'];
						$employee['blood_group'] = $row['blood_group'];
						$employee['reporting_manager'] = $row['reporting_manager'];
						$employee['remark'] = 'active';
						if(strtolower($row['remarks']))
							$employee['remark'] = strtolower($row['remarks']);	
						$employees[] = $employee;
					}
					else{
						$existEmployees[] = $row['emp._no.'];
					}
				}
			}
			$message = "All Employees created successfully";
			if(count($existEmployees)){
				$message = "These employee id ".implode(", ", $existEmployees)." are not inserted. Please check";
			}
			Employee::insert($employees);
			if(!empty($employeeIds)){
				$employees = Employee::whereIn('employee_id', $employeeIds)->get()->toArray();
				foreach ($employees as $employee) {
					$employeeLog = new EmployeeLog();
					$employeeLog->employee_id = $employee['id'];
					$employeeLog->action = 'Insert';    
					$employeeLog->user_id = Auth::user()->id;
					$employeeLog->department_id = $employee['department_id'];
					$employeeLog->category_id = $employee['category_id'];
					$employeeLog->location_id = $employee['location_id'];
					$employeeLog->cost_centre = $employee['cost_centre'];
					$employeeLog->gl_accounts = $employee['gl_accounts'];
					$employeeLog->save();
				}
			}
			return redirect()->route('admin.employees.index')->with('message', $message);
		}
	}

	private function formatData($model_details)
	{
		$model_data = [];
		foreach ($model_details as $model_detail) {
			$model_data[strtolower($model_detail->name)] = $model_detail->id;
		}
		return $model_data;
	}
	private function checkEmployeeID($employee_id)
	{
		$count = Employee::where('employee_id', $employee_id)->get()->count();
		if($count>0)
			return true;
		return false;
	}

}
