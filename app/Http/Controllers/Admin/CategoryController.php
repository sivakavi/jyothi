<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCategory;

class CategoryController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$categories = Category::orderBy('id', 'desc')->paginate(10);

		return view('admin.categories.index', compact('categories'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.categories.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(StoreCategory $request)
	{
		$category = new Category();

		$category->name = $request->input("name");

		$category->save();

		return redirect()->route('admin.categories.index')->with('message', 'Item created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$category = Category::findOrFail($id);

		return view('admin.categories.show', compact('category'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$category = Category::findOrFail($id);

		return view('admin.categories.edit', compact('category'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(StoreCategory $request, $id)
	{
		$category = Category::findOrFail($id);

		$category->name = $request->input("name");

		$category->save();

		return redirect()->route('admin.categories.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$category = Category::findOrFail($id);
		$category->delete();

		return redirect()->route('admin.categories.index')->with('message', 'Item deleted successfully.');
	}

}
