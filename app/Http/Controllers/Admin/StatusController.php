<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Status;
use App\Department;
use App\Http\Requests\StoreStatus;

class StatusController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$statuses = Status::orderBy('id', 'asc')->paginate(10);

		return view('admin.statuses.index', compact('statuses'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$departments = Department::all(['id', 'name']);
		return view('admin.statuses.create', compact('departments'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreStatus $request)
	{
		$status = new Status();

		$status->name = $request->input("name");
		$status->department_id = $request->input("department_id");
		$status->save();

		return redirect()->route('admin.statuses.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$status = Status::findOrFail($id);

		return view('admin.statuses.show', compact('status'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$status = Status::findOrFail($id);
		$departments = Department::all(['id', 'name']);

		return view('admin.statuses.edit', compact('status', 'departments'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreStatus $request, $id)
	{
		$status = Status::findOrFail($id);

		$status->name = $request->input("name");
		$status->department_id = $request->input("department_id");
		$status->save();

		return redirect()->route('admin.statuses.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$status = Status::findOrFail($id);
		$status->delete();

		return redirect()->route('admin.statuses.index')->with('message', 'Item deleted successfully.');
	}

}
