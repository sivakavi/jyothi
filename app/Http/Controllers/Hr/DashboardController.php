<?php

namespace App\Http\Controllers\Hr;

use App\Models\Auth\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use App\Employee;
use App\Shift;
use App\Department;
use App\Leave;
use App\Status;
use App\WorkType;
use App\AssignShift;
use App\Batch;
use Auth;
use Illuminate\Support\Facades\Input;

class DashboardController extends Controller
{
    private $user;

    private $department_id;

    private $shift_id;

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
        $batches = Batch::groupBy('department_id')->paginate(10);
        return view('hr.batch', compact('batches'));
    }

    public function bulkConfirmedShift(Request $request)
    {
        $employeeDetails        = $request->get('employeeDetails');
        $rejectedEmployees = [];
        foreach($employeeDetails as $employeeDetail){
            $check = $this->bulkCheck($employeeDetail['batch_id'], $employeeDetail['emp_id'], $employeeDetail['empDatepickerFrom'], $employeeDetail['empDatepickerTo']);
            if($check != 'true'){
                $rejectedEmployees[]=$check;
            }
            else{
                $empDatepickerFrom      = new \DateTime($employeeDetail['empDatepickerFrom']);
                $empDatepickerTo        = new \DateTime($employeeDetail['empDatepickerTo']);
                $this->employeeShiftInsert($employeeDetail['emp_id'], $employeeDetail['work_types'], $employeeDetail['shifts'], $employeeDetail['emp_status'], $empDatepickerFrom, $empDatepickerTo, $employeeDetail['batch_id']);
            }
        }
        if(count($rejectedEmployees)){
            return $rejectedEmployees;
        }
        return 'true';
    }

    private function bulkCheck($batch_id, $employee_id, $empDatepickerFrom, $empDatepickerTo){
        $empDatepickerFrom      = new \DateTime($empDatepickerFrom);
        $empDatepickerTo        = new \DateTime($empDatepickerTo);
        $empDatepickerFromCount = Batch::whereBetween('fromDate', [$empDatepickerFrom, $empDatepickerTo])->where('id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        $empDatepickerToCount   = Batch::whereBetween('toDate', [$empDatepickerFrom, $empDatepickerTo])->where('id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        if($empDatepickerFromCount || $empDatepickerToCount)
            return $batch_id;
        return 'true';
    }

    public function shift(Request $request)
    {
        $department_id = $request->department_id;
        $batches = Batch::where('department_id', $department_id)->where('status', 'pending')->get()->toArray();
        
        foreach ($batches as $key => $batch) {
            $employeeShift = AssignShift::where('batch_id', $batch['id'])->where('employee_id', $batch['employee_id'])->first();

            $batches[$key]['shift_id'] = $employeeShift->shift_id;
            $batches[$key]['employee_name'] = $employeeShift->employee->name;
            $batches[$key]['category_name'] = $employeeShift->employee->category->name;
            $batches[$key]['work_type_id'] = $employeeShift->work_type_id;
            $batches[$key]['status_id'] = $employeeShift->status_id;
            ;
        }
        $assignShifts = $batches;
        // dd($batches);
        // $assignShifts = AssignShift::whereIn('batch_id', $batchIds)->paginate(10);
        $shifts = Shift::where('department_id', $department_id)->get();
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        return view('hr.shift', compact('assignShifts', 'shifts', 'statuses', 'work_types'));
    }

    public function assignEmpShiftCheck(Request $request)
    {
        $employee_id            = $request->get('employee_id');
        $batch_id               = $request->get('batch_id');
        $empDatepickerFrom      = new \DateTime( $request->get('empDatepickerFrom'));
        $empDatepickerTo        = new \DateTime( $request->get('empDatepickerTo'));
        $empDatepickerFromCount = Batch::whereBetween('fromDate', [$empDatepickerFrom, $empDatepickerTo])->where('id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        $empDatepickerToCount   = Batch::whereBetween('toDate', [$empDatepickerFrom, $empDatepickerTo])->where('id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        if($empDatepickerFromCount || $empDatepickerToCount)
            return 'false';
        else{
            $work_type_id           = $request->get('work_type_id');
            $shift_id               = $request->get('shift_id');
            $status_id              = $request->get('status_id');
            $batch_id               = $request->get('batch_id');
            return $this->employeeShiftInsert($employee_id, $work_type_id, $shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo, $batch_id);
        }
    }

    private function employeeShiftInsert($employee_id, $work_type_id, $shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo, $batch_id)
    {
        
        $department_id  = Employee::find($employee_id)->department_id;
        $batch  = Batch::find($batch_id);
        $batch->fromDate = $empDatepickerFrom;
        $batch->toDate = $empDatepickerTo;
        $batch->status = 'confirmed';
        $batch->save();

        $employeeRecords = [];
        AssignShift::where('batch_id', $batch->id)->delete();
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

    public function assignEmpShiftAttendance()
    {
        $departments = Department::all();
        return view('hr.empAttendance', compact('departments'));
    }

    public function getShift(Request $request)
    {
        $department_id = $request->input('department_id');
        $shifts = Shift::all(['id', 'name', 'department_id'])->where('department_id',$department_id);
        $data = [];
        foreach($shifts as $shift){
            $data[$shift->id] = $shift->name;
        }
        return response()->json($data);
    }

    public function shiftDetails(Request $request)
    {
        $department_id = $request->get('department_id');
        $this->department_id = $department_id;
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        $leaves = Leave::all();
        $date = $request->get('date');
        $this->shift_id = $request->get('shift_id');
        $employees = AssignShift::where('nowdate', $date)
                                ->where(function ($q) {
                                    $q->where('shift_id', $this->shift_id)
                                    ->orWhere('changed_shift_id', $this->shift_id);
                                })
                                ->where(function ($q) {
                                    $q->where('department_id', $this->department_id)
                                    ->orWhere('changed_department_id', $this->department_id);
                                })->paginate(10)
                                ;
        $variables = ['employees' => $employees->appends(Input::except('page')),
                        'statuses' => $statuses,
                        'leaves' => $leaves,
                        'work_types' => $work_types,
                     ];
        return view('hr.shiftDetails', $variables);
    }

    public function employeeSearch(Request $request)
    {
        $department_id = $request->get('department_id');
        $emp_name = $request->get('name');
        $employees = [];
        $employeeDetails = Employee::where('employee_id', $emp_name)->get();
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
        $work_type_id = $request->get('emp_work_type');
        $assignshift = AssignShift::find($id);

        $assignshift->status_id = $status_id;
        $assignshift->work_type_id = $work_type_id;
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

    public function employeeAdd(Request $request)
    {
        $department_id = $request->get('department_id');
        $empId = $request->get('emp_id');
        $work_type_id = $request->get('emp_work_type');
        $status_id = $request->get('status');
        $leave_id = $request->get('leave');
        $otHours = $request->get('othours');
        $shift_id = $request->get('shift_id');
        $empDate = new \DateTime($request->get('empDate'));
        if($leave_id == 'false'){
            $leave_id = NULL; 
        }
        if($otHours == 'false'){
            $otHours = NULL; 
        }
        $emp = AssignShift::where('employee_id', $empId)->where('nowdate', $empDate)->first();
        if($emp){
            $emp->changed_department_id = $department_id;
            $emp->changed_shift_id = $shift_id;
            $emp->leave_id = $leave_id;
            $emp->otHours = $otHours;
            $emp->status_id = $status_id;
            $emp->work_type_id = $work_type_id;
            $emp->save();
            return 'true';
        }
        else{
            $employee_details = Employee::find($empId);
            $defaultshifts = Employee::find(4)->department->shifts->first->get()->toArray();
            $batch = new Batch();
            $batch->department_id = $defaultshifts['department_id'];
            $batch->employee_id = $empId;
            $batch->fromDate = $empDate;
            $batch->toDate = $empDate;
            $batch->status = 'confirmed';
            $batch->save();
            $data = array('department_id'=>$defaultshifts['department_id'], 'batch_id'=> $batch->id, 'employee_id'=> $empId, 'shift_id'=> $defaultshifts['id'], 'work_type_id'=> $work_type_id, 'status_id'=> $status_id, 'leave_id'=> $leave_id, 'otHours'=> $otHours, 'nowdate'=> $empDate, 'changed_department_id' => $department_id, 'changed_shift_id' => $shift_id);
            AssignShift::insert($data);
            return 'true';
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

    public function reportPage()
    {
        $departments = Department::all(['id', 'name']);
        return view('hr.report', $departments);
    }
   
}