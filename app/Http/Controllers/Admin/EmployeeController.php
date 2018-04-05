<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Employee;
use App\Category;
use App\Location;
use App\Department;
use App\Http\Requests\StoreEmployee;
use Illuminate\Http\Request;
use Validator;


class EmployeeController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$employees = Employee::orderBy('id', 'asc')->paginate(10);

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
		$employee->save();

		return redirect()->route('admin.employees.index')->with('message', 'Item created successfully.');
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
		$employee = Employee::findOrFail($id);
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
		$employee = Employee::findOrFail($id);

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

		$employee->save();

		return redirect()->route('admin.employees.index')->with('message', 'Item updated successfully.');
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

		return redirect()->route('admin.employees.index')->with('message', 'Item deleted successfully.');
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
			foreach($rows as $row){
				if($row['emp._no.'] != ''){
					$exist = $this->checkEmployeeID($row['emp._no.']);
					if(!$exist){
						$employee['name'] = $row['name'];
						$employee['gender'] = $row['gender'];
						$employee['employee_id'] = $row['emp._no.'];
						$employee['department_id'] = $departments[strtolower($row['department'])];
						$employee['category_id'] = $categories[strtolower($row['category'])];
						$employee['location_id'] = $locations[strtolower($row['location'])];
						$employee['cost_centre'] = $row['cost_centre'];
						$employee['cost_centre_desc'] = $row['cost_centre_desc'];
						$employee['gl_accounts'] = $row['gl_accounts'];
						$employee['gl_description'] = $row['gl_discription'];	
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
