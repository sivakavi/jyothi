<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Employee;
use App\Category;
use App\Location;
use App\Department;
use App\Http\Requests\StoreEmployee;

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

}
