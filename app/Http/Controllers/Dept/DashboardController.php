<?php

namespace App\Http\Controllers\Dept;

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
        return view('dept.dashboard');
    }

    public function shiftBatch()
    {
        $batches = Batch::orderBy('id', 'asc')->paginate(10);
        return view('dept.batch', compact('batches'));
    }

    public function shift()
    {
        $department_id = $this->user->department->id;
        $employees = Employee::where('department_id', $department_id)->get();
        $shifts = Shift::where('department_id', $department_id)->get();
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        return view('dept.shift', compact('employees', 'shifts', 'statuses', 'work_types'));
    }

    public function assignShiftCheck(Request $request)
    {
        $department_id = $this->user->department->id;
        $date = new \DateTime( $request->get('date'));
        $count = AssignShift::where('department_id', $department_id)->where('nowdate',$date)->count();
        if($count)
            return 'false';
        return 'true';
    }

    public function assignShift(Request $request)
    {
        $employeeDetails = $request->get('employeeDetails');
        
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $begin = new \DateTime( $fromDate );
        $end   = new \DateTime( $toDate );

        $department_id = $this->user->department->id;

        $batch = new Batch();

        $batch->department_id = $department_id;
        $batch->fromDate = $begin;
        $batch->toDate = $end;
        $batch->status = 'pending';
        $batch->save();

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