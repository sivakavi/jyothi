<?php

namespace App\Http\Controllers\Hr;

use App\Models\Auth\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use App\Employee;
use App\Shift;
use App\Status;
use App\WorkType;
use App\AssignShift;
use App\Batch;
use Auth;

class DashboardController extends Controller
{
    private $user;

    // private $college;

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            // $this->college = $this->user->college;
            return $next($request);
        });
    }

    public function index()
    {
        return view('hr.dashboard');
    }

    public function shiftBatch()
    {
        $batches = Batch::orderBy('id', 'asc')->paginate(10);
        return view('hr.batch', compact('batches'));
    }

    public function shift(Request $request)
    {
        $batch_id = $request->batch_id;
        $assignShifts = AssignShift::where('batch_id', $batch_id)->groupBy('employee_id')->get();
        $department_id = $request->department_id;
        $shifts = Shift::where('department_id', $department_id)->get();
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        return view('hr.shift', compact('assignShifts', 'shifts', 'statuses', 'work_types'));
    }

    public function assignShift(Request $request)
    {
        $employeeDetails = $request->get('employeeDetails');
        $batch_id = $request->get('batch_id');
        

        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $begin = new \DateTime( $fromDate );
        $end   = new \DateTime( $toDate );

        $department_id = $request->get('department_id');;

        $batch = Batch::findOrFail($batch_id);;

        $batch->department_id = $department_id;
        $batch->fromDate = $begin;
        $batch->toDate = $end;
        $batch->status = 'confirmed';
        $batch->save();

        $assignShift = AssignShift::where('batch_id', $batch_id);
        $assignShift->delete();
        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            foreach($employeeDetails as $employeedetail){
                $data = array('department_id'=>$department_id, 'batch_id'=> $batch->id, 'employee_id'=> $employeedetail['emp_id'], 'shift_id'=> $employeedetail['shifts'], 'work_type_id'=> $employeedetail['work_types'], 'status_id'=> $employeedetail['emp_status'], 'leave_id'=> null, 'otHours'=> null, 'nowdate'=> $nowdate);

                $employees[] = $data;
            }
        }

        AssignShift::insert($employees);
        return 'true';
    }
   
}