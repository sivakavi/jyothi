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
		$worktypes = WorkType::orderBy('id', 'asc')->paginate(10);

		return view('admin.worktypes.index', compact('worktypes'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$departments = Department::all(['id', 'name']);
		return view('admin.worktypes.create', compact('departments'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreWorkType $request)
	{
		$worktype = new WorkType();

		$worktype->name = $request->input("name");
		$worktype->department_id = $request->input("department_id");
		$worktype->save();

		return redirect()->route('admin.worktypes.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$worktype = WorkType::findOrFail($id);

		return view('admin.worktypes.show', compact('worktype'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$worktype = WorkType::findOrFail($id);
		$departments = Department::all(['id', 'name']);

		return view('admin.worktypes.edit', compact('worktype', 'departments'));
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
		$worktype = WorkType::findOrFail($id);

		$worktype->name = $request->input("name");
		$worktype->department_id = $request->input("department_id");
		$worktype->save();

		return redirect()->route('admin.worktypes.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$worktype = WorkType::findOrFail($id);
		$worktype->delete();

		return redirect()->route('admin.worktypes.index')->with('message', 'Item deleted successfully.');
	}

}
