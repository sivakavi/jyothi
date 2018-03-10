<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Leave;
use App\Http\Requests\StoreLeave;

class LeaveController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$leaves = Leave::orderBy('id', 'asc')->paginate(10);

		return view('admin.leaves.index', compact('leaves'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.leaves.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreLeave $request)
	{
		$leave = new Leave();

		$leave->name = $request->input("name");
		$leave->save();

		return redirect()->route('admin.leaves.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$leave = Leave::findOrFail($id);

		return view('admin.leaves.show', compact('leave'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$leave = Leave::findOrFail($id);

		return view('admin.leaves.edit', compact('leave'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreLeave $request, $id)
	{
		$leave = Leave::findOrFail($id);

		$leave->name = $request->input("name");

		$leave->save();

		return redirect()->route('admin.leaves.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$leave = Leave::findOrFail($id);
		$leave->delete();

		return redirect()->route('admin.leaves.index')->with('message', 'Item deleted successfully.');
	}

}
