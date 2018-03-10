<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Shift;
use App\Http\Requests\StoreShift;

class ShiftController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$shifts = Shift::orderBy('id', 'asc')->paginate(10);

		return view('admin.shifts.index', compact('shifts'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.shifts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreShift $request)
	{
		$shift = new Shift();

		$shift->name = $request->input("name");
		$shift->department_id = $request->input("department_id");
		$shift->allias = $request->input("allias");
		$shift->in = $request->input("in");
		$shift->out = $request->input("out");
		$shift->save();

		return redirect()->route('admin.shifts.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$shift = Shift::findOrFail($id);

		return view('admin.shifts.show', compact('shift'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$shift = Shift::findOrFail($id);

		return view('admin.shifts.edit', compact('shift'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreShift $request, $id)
	{
		$shift = Shift::findOrFail($id);

		$shift->name = $request->input("name");
		$shift->department_id = $request->input("department_id");
		$shift->allias = $request->input("allias");
		$shift->in = $request->input("in");
		$shift->out = $request->input("out");

		$shift->save();

		return redirect()->route('admin.shifts.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$shift = Shift::findOrFail($id);
		$shift->delete();

		return redirect()->route('admin.shifts.index')->with('message', 'Item deleted successfully.');
	}

}
