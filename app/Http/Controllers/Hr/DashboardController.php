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
use App\Holiday;
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
        $departments = Department::all()->count();
        $shifts = Batch::where('status', 'pending')->count();
        return view('hr.dashboard', compact('departments', 'shifts'));
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
                // $empDatepickerFrom      = new \DateTime($employeeDetail['empDatepickerFrom']);
                // $empDatepickerTo        = new \DateTime($employeeDetail['empDatepickerTo']);
                $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $employeeDetail['empDatepickerFrom']);
                $empDatepickerTo      = Carbon::createFromFormat('d/m/Y', $employeeDetail['empDatepickerTo']);
                $this->employeeShiftInsert($employeeDetail['emp_id'], $employeeDetail['work_types'], $employeeDetail['shifts'], $employeeDetail['emp_status'], $empDatepickerFrom, $empDatepickerTo, $employeeDetail['batch_id']);
            }
        }
        if(count($rejectedEmployees)){
            return $rejectedEmployees;
        }
        return 'true';
    }

    private function bulkCheck($batch_id, $employee_id, $empDatepickerFrom, $empDatepickerTo){
        // $empDatepickerFrom      = new \DateTime($empDatepickerFrom);
        // $empDatepickerTo        = new \DateTime($empDatepickerTo);
        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $empDatepickerFrom);
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $empDatepickerTo);
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
        // $empDatepickerFrom      = new \DateTime( $request->get('empDatepickerFrom'));
        // $empDatepickerTo        = new \DateTime( $request->get('empDatepickerTo'));

        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'));
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'));
        
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
        $holidays = Holiday::all()->pluck('holiday_at')->toArray();
        AssignShift::where('batch_id', $batch->id)->delete();
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $day_num = $i->format("N");
            if($day_num < 7 && !in_array($nowdate, $holidays)) { /* weekday */
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
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
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
                                })->whereNotIn('batch_id', $pendingBatches)->paginate(10)
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
        // $empDate = new \DateTime($request->get('empDate'));
        $empDate      = Carbon::createFromFormat('d/m/Y', $request->get('empDate'));
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

    public function getDepartmentEmployee(Request $request)
    {
        $department_id = $request->input('department_id');
        $employees = Employee::where('department_id',$department_id)->get();
        $data = [];
        foreach($employees as $employee){
            $data[$employee->id] = $employee->name.' - '. $employee->employee_id;
        }
        return response()->json($data);
    }

    public function reportPage()
    {
        return view('hr.report');
    }

    public function reportEmployeePage()
    {
        $departments = Department::all(['id', 'name']);
        return view('hr.reportEmployee', compact('departments'));
    }

    public function getReport(Request $request)
    {
        // $fromDate = new \DateTime( $request->get('fromDate'));
        // $toDate = new \DateTime( $request->get('toDate'));
        $fromDate   = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'));
        $toDate     = Carbon::createFromFormat('d/m/Y', $request->get('toDate'));
        $fieldArray = $request->get('fieldArray');
        $fieldArray = explode(',', $fieldArray);
        $upperCaseFieldArray = array_map('strtoupper', $fieldArray);

        if($request->has('employee_id')){
            $emp_id = $request->get('employee_id');
            $report = AssignShift::where('employee_id',$emp_id)->whereBetween('nowdate', [$fromDate, $toDate])->get();
        }else{
            $report = AssignShift::whereBetween('nowdate', [$fromDate, $toDate])->get();
        }
        
        
        $finalArray = [];
        //$finalArray[] = $upperCaseFieldArray;

        foreach($report as $singleRow){
            $singleItem = [];
            
            if (in_array("work_dept_name", $fieldArray)) {
                if($singleRow->changed_department_id){
                    $singleItem["work_dept_name"] = $singleRow->changed_department->name;
                }else{
                    $singleItem["work_dept_name"] = $singleRow->department->name;
                }
            }

            if (in_array("work_dept_code", $fieldArray)) {
                if($singleRow->changed_department_id){
                    $singleItem["work_dept_code"] = $singleRow->changed_department->department_code;
                }else{
                    $singleItem["work_dept_code"] = $singleRow->department->department_code;
                }
            }

            if (in_array("shift_name", $fieldArray)) {
                if($singleRow->changed_shift_id){
                    $singleItem["shift_name"] = $singleRow->changed_shift->name;
                }else{
                    $singleItem["shift_name"] = $singleRow->shift->name;
                }
            }

            if (in_array("shift_code", $fieldArray)) {
                if($singleRow->changed_shift_id){
                    $singleItem["shift_code"] = $singleRow->changed_shift->allias;
                }else{
                    $singleItem["shift_code"] = $singleRow->shift->allias;
                }
            }

            if (in_array("shift_date", $fieldArray)) {
                $singleItem["shift_date"] = $singleRow->nowdate;
            }

            if (in_array("status", $fieldArray)) {
                $singleItem["status"] = $singleRow->status->name;
            }

            if (in_array("process", $fieldArray)) {
                $singleItem["process"] = $singleRow->work_type->name;
            }

            if (in_array("leave_type", $fieldArray)) {
                if($singleRow->leave){
                    $singleItem["leave_type"] = $singleRow->leave->name;
                }else{
                    $singleItem["leave_type"] = "";
                }
                
            }

            if (in_array("ot_hours", $fieldArray)) {
                $singleItem["ot_hours"] = $singleRow->otHours;
            }

            if (in_array("emp_name", $fieldArray)) {
                $singleItem["emp_name"] = $singleRow->employee->name;
            }

            if (in_array("emp_dept_name", $fieldArray)) {
                $singleItem["emp_dept_name"] = $singleRow->employee->department->name;
            }

            if (in_array("emp_dep_code", $fieldArray)) {
                $singleItem["emp_dep_code"] = $singleRow->employee->department->department_code;
            }

            if (in_array("emp_code", $fieldArray)) {
                $singleItem["emp_code"] = $singleRow->employee->employee_id;
            }

            if (in_array("cost_centre", $fieldArray)) {
                $singleItem["cost_centre"] = $singleRow->employee->cost_centre;
            }

            if (in_array("cost_centre_desc", $fieldArray)) {
                $singleItem["cost_centre_desc"] = $singleRow->employee->cost_centre_desc;
            }

            if (in_array("gl_account", $fieldArray)) {
                $singleItem["gl_account"] = $singleRow->employee->gl_accounts;
            }

            if (in_array("gl_account_desc", $fieldArray)) {
                $singleItem["gl_account_desc"] = $singleRow->employee->gl_description;
            }

            if (in_array("location", $fieldArray)) {
                $singleItem["location"] = $singleRow->employee->location->name;
            }

            if (in_array("category", $fieldArray)) {
                $singleItem["category"] = $singleRow->employee->category->name;
            }

            if (in_array("gender", $fieldArray)) {
                $singleItem["gender"] = $singleRow->employee->gender;
            }

            $finalArray[] = $singleItem;
        }

        return $finalArray;
    }
   
}