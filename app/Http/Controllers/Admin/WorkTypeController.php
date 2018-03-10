<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\WorkType;
use App\Department;
use App\Http\Requests\StoreWorkType;

class WorkTypeController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$work_types = WorkType::orderBy('id', 'asc')->paginate(10);
		
		return view('admin.work_types.index', compact('work_types'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$departments = Department::all(['id', 'name']);
		return view('admin.work_types.create', compact('departments'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreWorkType $request)
	{
		$work_type = new WorkType();

		$work_type->name = $request->input("name");
		$work_type->department_id = $request->input("department_id");
		$work_type->save();

		return redirect()->route('admin.work_types.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$work_type = WorkType::findOrFail($id);

		return view('admin.work_types.show', compact('work_type'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$work_type = WorkType::findOrFail($id);
		$departments = Department::all(['id', 'name']);

		return view('admin.work_types.edit', compact('work_type', 'departments'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreWorkType $request, $id)
	{
		$work_type = WorkType::findOrFail($id);

		$work_type->name = $request->input("name");
		$work_type->department_id = $request->input("department_id");
		$work_type->save();

		return redirect()->route('admin.work_types.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$work_type = WorkType::findOrFail($id);
		$work_type->delete();

		return redirect()->route('admin.work_types.index')->with('message', 'Item deleted successfully.');
	}

}
