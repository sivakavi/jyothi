<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Department;
use App\Http\Requests\StoreDepartment;

class DepartmentController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$departments = Department::orderBy('id', 'asc')->paginate(10);

		return view('admin.departments.index', compact('departments'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.departments.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreDepartment $request)
	{
		$department = new Department();

		$department->name = $request->input("name");
		$department->save();

		return redirect()->route('admin.departments.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$department = Department::findOrFail($id);

		return view('admin.departments.show', compact('department'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$department = Department::findOrFail($id);

		return view('admin.departments.edit', compact('department'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreDepartment $request, $id)
	{
		$department = Department::findOrFail($id);

		$department->name = $request->input("name");

		$department->save();

		return redirect()->route('admin.departments.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$department = Department::findOrFail($id);
		$department->delete();

		return redirect()->route('admin.departments.index')->with('message', 'Item deleted successfully.');
	}

}
