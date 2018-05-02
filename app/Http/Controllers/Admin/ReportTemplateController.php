<?php namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ReportTemplate;
use Illuminate\Http\Request;

class ReportTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $report_template = ReportTemplate::orderBy('id', 'asc')->paginate(10);

		return view('admin.report_templates.index', compact('report_template'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.report_templates.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rt = new ReportTemplate();

        $rt->name = $request->input("name");
        $rt->frontend_data = $request->input("frontend_data");
        $rt->backend_data = $request->input("backend_data");
        $rt->report_type = 1;
		$rt->save();

		return redirect()->route('admin.report_templates.index')->with('message', 'Report Template created successfully.');
    }


    public function saveTemplate(Request $request)
    {
        $rt = new ReportTemplate();

        $reportTemplate = $request->get('templateData');

        $rt->name = $reportTemplate['name'];
        $rt->frontend_data = $reportTemplate['frontend_data'];
        $rt->backend_data = $reportTemplate['backend_data'];
        $rt->report_type = 1;
		$rt->save();

		return "true";
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rt = ReportTemplate::findOrFail($id);

		return view('admin.report_templates.show', compact('rt'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rt = ReportTemplate::findOrFail($id);

		return view('admin.report_templates.edit', compact('rt'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rt = ReportTemplate::findOrFail($id);

		$rt->name = $request->input("name");
        $rt->frontend_data = $request->input("frontend_data");
        $rt->backend_data = $request->input("backend_data");
        $rt->report_type = 1;

		$rt->save();

		return redirect()->route('admin.report_templates.index')->with('message', 'Report Template updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rt = ReportTemplate::findOrFail($id);
		$rt->delete();

		return redirect()->route('admin.report_templates.index')->with('message', 'Report Template deleted successfully.');
    }
}
