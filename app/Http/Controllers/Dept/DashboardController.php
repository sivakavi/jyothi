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
use App\Leave;
use Auth;
use Illuminate\Support\Facades\Input;

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

    public function individualSelect(Request $request)
    {
        $fromDate           = new \DateTime( $request->get('empDatepicker'));
        $toDate             = new \DateTime($request->get('fromDate'));
        $empId              = $request->get('emp-id');
        $count              = AssignShift::where('employee_id', $empId)->whereBetween('nowdate', [$fromDate, $toDate])->get()->count();
        if($count)
            return 'false';
        return 'true';
    }

    public function bulkSelect(Request $request)
    {
        $fromDate           = new \DateTime( $request->get('empDatepicker'));
        $toDate             = new \DateTime($request->get('fromDate'));
        $department_id      = $this->user->department->id;
        $employees = Employee::where('department_id', $department_id)->select('id')->get()->toArray();
        $empID = [];
        foreach ($employees as $key => $emp) {
            $count = AssignShift::where('employee_id', $emp['id'])->whereBetween('nowdate', [$fromDate, $toDate])->get()->count();

            if($count > 0){
                $empID[] = $emp['id'];
            }
        }
        return response()
            ->json($empID);
    }

    public function assignEmpShiftIndividual(Request $request)
    {
        $employee_id            = $request->get('emp_id');
        $empDatepicker          = new \DateTime( $request->get('empDatepicker'));
        if($request->has('fromDate')){
            $fromDate           = new \DateTime( $request->get('fromDate'));
            $empDatepickerCount = AssignShift::where('employee_id', $employee_id)->whereBetween('nowdate', [$fromDate, $empDatepicker])->get()->count();
        }
        else{
            $empDatepickerCount     = $this->employeeShiftCount($employee_id, $empDatepicker);
        }
        if($empDatepickerCount)
            return 'false';
        return 'true';
    }

    public function assignEmpShiftCheck(Request $request)
    {
        $employee_id            = $request->get('employee_id');
        $empDatepickerFrom      = new \DateTime( $request->get('empDatepickerFrom'));
        $empDatepickerTo        = new \DateTime( $request->get('empDatepickerTo'));
        $empDatepickerCount     = AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->get()->count();
        if($empDatepickerCount)
            return 'false';
        else{
            $work_type_id           = $request->get('work_type_id');
            $shift_id               = $request->get('shift_id');
            $status_id              = $request->get('status_id');
            return $this->employeeShiftInsert($employee_id, $work_type_id, $shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo);
        }
        

    }

    public function assignShift(Request $request)
    {
        $employeeDetails = $request->get('employeeDetails');
        foreach($employeeDetails as $employeedetail){
            $employee_id = $employeedetail['emp_id'];
            $work_type_id = $employeedetail['work_types'];
            $shift_id = $employeedetail['shifts'];
            $status_id = $employeedetail['emp_status'];
            $empDatepickerFrom = new \DateTime( $employeedetail['empDatepickerFrom']);
            $empDatepickerTo = new \DateTime( $employeedetail['empDatepickerTo']);
            $this->employeeShiftInsert($employee_id, $work_type_id, $shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo);
        }
        return 'true';
    }

    public function shiftList()
    {
        // dd($this->isWeekend(date('Y-m-d')));
        $department_id = $this->user->department->id;
        // dd($department_id);
        $shifts = Shift::where('intime', '<', '02:00:00')->where('department_id', $department_id)->orderBy('intime', 'desc')->take(3)->get()->toArray();
        foreach ($shifts as $key => $value) {
            $shiftDetails[] = $this->shiftdetailsformat($value, date('Y-m-d'));
        }
        if(count($shifts)<3){
            $take = 3 - count($shifts);
            $previous_shifts = Shift::where('department_id', $department_id)->orderBy('outtime', 'desc')->take($take)->get()->toArray();
            foreach ($previous_shifts as $key => $value) {
                $shiftDetails[] = $this->shiftdetailsformat($value, date('Y-m-d',strtotime("-1 days")));
            } 
        }
        // dd($shiftDetails);
        return view('dept.shiftList', compact('shiftDetails'));
    }

    public function shiftDetails(Request $request)
    {
        $statuses = Status::where('department_id', $this->user->department->id)->get();
        $leaves = Leave::all();
        $date = $request->get('date');
        $shift_id = $request->get('shift_id');
        $employees = AssignShift::where('nowdate', $date)
                                ->where('shift_id', $shift_id)
                                ->where(function ($q) {
                                    $q->where('department_id', $this->user->department->id)
                                    ->orWhere('changed_department_id', $this->user->department->id);
                                })->paginate(10)
                                ;
        $variables = ['employees' => $employees->appends(Input::except('page')),
                        'statuses' => $statuses,
                        'leaves' => $leaves,
                     ];
        return view('dept.shiftDetails', $variables);
    }

    public function employeeSearch(Request $request)
    {
        $emp_name = $request->get('name');
        $employees = [];
        $employeeDetails = Employee::where('department_id', '!=', $this->user->department->id)->where('name', 'LIKE', strtolower($emp_name) . '%')->get();
        foreach ($employeeDetails as $employeeDetail) {
            $employee['id'] = $employeeDetail->id;
            $employee['name'] = $employeeDetail->name;
            $employee['department_name'] = $employeeDetail->department->name;
            $employees[] = $employee;
        }
        return \Response::json($employees);
    }
    public function shiftDetailsChange(Request $request)
    {

        $status_id = $request->get('status');
        $leave_id =  $request->get('leave');
        $id  = $request->get('assignShiftId');
        $othours = $request->get('othours');
        $assignshift = AssignShift::find($id);

        $assignshift->status_id = $status_id;
        $assignshift->leave_id = NULL;
        $assignshift->otHours = NULL;
        if($leave_id != 'false'){
            $assignshift->leave_id = $leave_id; 
        }
        if($othours != 'false'){
            $assignshift->otHours = $othours; 
        }
        $assignshift->save();
        return 'true';

    }

    private function employeeShiftCount($employee_id, $date){
        return AssignShift::where('employee_id', $employee_id)->where('nowdate',$date)->count();
    }

    private function employeeShiftInsert($employee_id, $work_type_id, $shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo)
    {
        
        $department_id          = Employee::find($employee_id)->department_id;
        $batch = new Batch();

        $batch->department_id = $department_id;
        $batch->employee_id = $employee_id;
        $batch->fromDate = $empDatepickerFrom;
        $batch->toDate = $empDatepickerTo;
        $batch->status = 'pending';
        $batch->save();

        $employeeRecords = [];
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $day_num = $i->format("N");
            if($day_num < 7) { /* weekday */
                    $data = array('department_id'=>$department_id, 'batch_id'=> $batch->id, 'employee_id'=> $employee_id, 'shift_id'=> $shift_id, 'work_type_id'=> $work_type_id, 'status_id'=> $status_id, 'leave_id'=> null, 'otHours'=> null, 'nowdate'=> $nowdate);
                    $employeeRecords[] = $data;
            }
        }
        AssignShift::insert($employeeRecords);
        return 'true';
    }

    public function employeeAdd(Request $request)
    {
        $empId = $request->get('empId');
        $empDate = new \DateTime($request->get('empDate'));
        $emp = AssignShift::where('employee_id', $empId)->where('nowdate', $empDate)->first();
        if($emp){
            $emp->changed_department_id = $this->user->department->id;
            $emp->save();
            return 'true';
        }
        else{
            return 'false';
        }
    }

    private function shiftdetailsformat($value, $date)
    {
        $shiftDetail['id']     = $value['id'];
        $shiftDetail['name']   = $value['name'];
        $shiftDetail['allias'] = $value['allias'];
        $shiftDetail['intime'] = $value['intime'];
        $shiftDetail['outtime'] = $value['outtime'];
        $shiftDetail['date'] = $date;
        return $shiftDetail;
    }

    public function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }
   
}