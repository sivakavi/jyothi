<?php

namespace App\Http\Controllers\Hr;

use App\Models\Auth\User\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use App\Employee;
use App\EmployeeLog;
use App\Shift;
use App\Department;
use App\Leave;
use App\Status;
use App\WorkType;
use App\Holiday;
use App\AssignShift;
use App\Batch;
use App\ReportTemplate;
use Auth;
use Illuminate\Support\Facades\Input;
use Hash;
use Validator;

class DashboardController extends Controller
{
    private $user;

    private $department_id;

    private $shift_id;

    private $dept;

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
        $holidayShifts = Batch::where('status', 'pending_holiday')->count();
        return view('hr.dashboard', compact('departments', 'shifts', 'holidayShifts'));
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
        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $empDatepickerFrom)->format("Y-m-d");
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $empDatepickerTo)->format("Y-m-d");
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

        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'))->format("Y-m-d");
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'))->format("Y-m-d");
        
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
        $empDatepickerFrom =  new \DateTime($empDatepickerFrom);
        $empDatepickerTo =  new \DateTime($empDatepickerTo);
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $day_num = $i->format("N");
            if($day_num < 7 && !in_array($nowdate, $holidays)) { /* weekday */
                    $data = array('department_id'=>$department_id, 'batch_id'=> $batch->id, 'employee_id'=> $employee_id, 'shift_id'=> $shift_id, 'work_type_id'=> $work_type_id, 'status_id'=> $status_id, 'leave_id'=> null, 'otHours'=> null, 'nowdate'=> $nowdate);
                    $employeeRecords[] = $data;
            }
        }
        AssignShift::insert($employeeRecords);
        if(count($employeeRecords)==0){
            $batch->delete();
            return 'false';
        }
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
        $department_id = $request->get('department_id');
        $status_id = $request->get('status');
        $leave_id =  $request->get('leave');
        $id  = $request->get('assignShiftId');
        $othours = $request->get('othours');
        $work_type_id = $request->get('emp_work_type');
        $assignshift = AssignShift::find($id);

        if(Status::find($status_id)->name != 'OT'){
            $assignshift->status_id = $status_id;
        }
        $assignshift->work_type_id = $work_type_id;
        $assignshift->leave_id = NULL;
        $assignshift->otHours = NULL;
        if($leave_id != 'false'){
            $assignshift->leave_id = $leave_id; 
        }
        if($othours != '0'){
            $assignshift->otHours = $othours;
            if($assignshift->employee->department->id != $department_id){
                $assignshift->ot_department_id = $department_id;
            } 
        }
        $assignshift->save();
        return 'true';

    }

    public function shiftBulkDetailsChange(Request $request)
    {
        $department_id = $request->get('department_id');
        $this->department_id = $department_id;
        $this->shift_id = $request->get('shift_id');
        $date      = $request->get('empDate');
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        $employees = AssignShift::where('nowdate', $date)
                                ->where(function ($q) {
                                    $q->where('shift_id', $this->shift_id)
                                    ->orWhere('changed_shift_id', $this->shift_id);
                                })
                                ->where(function ($q) {
                                    $q->where('department_id', $this->department_id)
                                    ->orWhere('changed_department_id', $this->department_id);
                                })->whereNotIn('batch_id', $pendingBatches)->pluck('id')->toArray()
                                ;
        $status_id = $request->get('status');
        $leave_id =  $request->get('leave');
        $id  = $request->get('assignShiftId');
        $othours = $request->get('othours');
        $work_type_id = $request->get('emp_work_type');
        foreach ($employees as $key => $id) {
            $assignshift = AssignShift::find($id);
            if(Status::find($status_id)->name != 'OT'){
                $assignshift->status_id = $status_id;
            }
            $assignshift->work_type_id = $work_type_id;
            $assignshift->leave_id = NULL;
            $assignshift->otHours = NULL;
            if($leave_id != 'false'){
                $assignshift->leave_id = $leave_id; 
            }
            if($othours != '0'){
                $assignshift->otHours = $othours; 
                if($assignshift->employee->department->id != $department_id){
                    $assignshift->ot_department_id = $department_id;
                }
            }
            $assignshift->save();   
        }
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
        $empDate      = $request->get('empDate');
        if($leave_id == 'false'){
            $leave_id = NULL; 
        }
        if($otHours == '0'){
            $otHours = NULL; 
        }
        $emp = AssignShift::where('employee_id', $empId)->where('nowdate', $empDate)->first();
        if($emp){
            $emp->changed_department_id = $department_id;
            $emp->changed_shift_id = $shift_id;
            $emp->leave_id = $leave_id;
            $emp->otHours = $otHours;
            if(Status::find($status_id)->name != 'OT'){
                $emp->status_id = $status_id;
            }      
            $emp->work_type_id = $work_type_id;
            if($otHours){
                $emp->ot_department_id = $department_id;
            }
            $emp->save();
            return 'true';
        }
        else{
            if(!is_null($otHours)){
                return 'false';
            }
            $employee_details = Employee::find($empId);
            $defaultshifts = Employee::find($empId)->department->shifts->first->get()->toArray();
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
        $report_templates = ReportTemplate::all(['id', 'name', 'frontend_data', 'backend_data']);
        return view('hr.report', compact('report_templates'));
    }

    public function reportEmployeePage()
    {
        $departments = Department::all(['id', 'name']);
        $report_templates = ReportTemplate::all(['id', 'name', 'frontend_data', 'backend_data']);
        return view('hr.reportEmployee', compact('departments', 'report_templates'));
    }

    public function getReport(Request $request)
    {
        // $fromDate = new \DateTime( $request->get('fromDate'));
        // $toDate = new \DateTime( $request->get('toDate'));
        $fromDate   = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->subDay();
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
            $nowdate = $singleRow->nowdate.' 23:59:59';
            $log = $singleRow->employee->employeeLogsLatestUpdate($nowdate);
            
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
                $singleItem["shift_date"] = $singleRow->nowdate->format('d/m/Y');
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
                $singleItem["ot_hours"] = 0;
                if($singleRow->otHours){
                    $singleItem["ot_hours"] = $singleRow->otHours;
                }
            }

            if (in_array("ot_department", $fieldArray)) {
                if($singleRow->ot_department_id){
                    $singleItem["ot_department"] = $singleRow->ot_department->name;
                }else{
                    $singleItem["ot_department"] = $singleRow->department->name;
                }
                
            }

            if (in_array("emp_name", $fieldArray)) {
                $singleItem["emp_name"] = $singleRow->employee->name;
            }

            if (in_array("emp_dept_name", $fieldArray)) {
                if($log){
                    $singleItem["emp_dept_name"] = $log->department->name;
                }
                else{
                    $singleItem["emp_dept_name"] = $singleRow->employee->department->name;
                }
            }

            if (in_array("emp_dep_code", $fieldArray)) {
                if($log){
                    $singleItem["emp_dep_code"] = $log->department->department_code;
                }
                else{
                    $singleItem["emp_dep_code"] = $singleRow->employee->department->department_code;
                }
            }

            if (in_array("emp_code", $fieldArray)) {
                $singleItem["emp_code"] = $singleRow->employee->employee_id;
            }

            if (in_array("cost_centre", $fieldArray)) {
                if($log){
                    $singleItem["cost_centre"] = $log->cost_centre;
                } else {
                    $singleItem["cost_centre"] = $singleRow->employee->cost_centre;
                }
            }

            if (in_array("cost_centre_desc", $fieldArray)) {
                $singleItem["cost_centre_desc"] = $singleRow->employee->cost_centre_desc;
            }

            if (in_array("gl_account", $fieldArray)) {
                if($log){
                    $singleItem["gl_account"] = $log->gl_accounts;
                } else {
                    $singleItem["gl_account"] = $singleRow->employee->gl_accounts;
                }
            }

            if (in_array("gl_account_desc", $fieldArray)) {
                $singleItem["gl_account_desc"] = $singleRow->employee->gl_description;
            }

            if (in_array("location", $fieldArray)) {
                if($log){
                    $singleItem["location"] = $log->location->name;
                } else{
                    $singleItem["location"] = $singleRow->employee->location->name;
                }
            }

            if (in_array("category", $fieldArray)) {
                if($log){
                    $singleItem["category"] = $log->category->name;
                }else{
                    $singleItem["category"] = $singleRow->employee->category->name;
                }
            }

            if (in_array("gender", $fieldArray)) {
                $singleItem["gender"] = $singleRow->employee->gender;
            }

            $finalArray[] = $singleItem;
        }

        return $finalArray;
    }

    public function holidayBatch()
    {
        $batches = Batch::groupBy('department_id')->paginate(10);
        return view('hr.holidayBatch', compact('batches'));
    }

    public function holidayShift(Request $request)
    {
        $department_id = $request->department_id;
        $batches = Batch::where('department_id', $department_id)->where('status', 'pending_holiday')->get()->toArray();
        
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
        
        $shifts = Shift::where('department_id', $department_id)->get();
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        return view('hr.holidayShift', compact('assignShifts', 'shifts', 'statuses', 'work_types'));
    }

    public function holidayShiftAssign(Request $request)
    {
        $employee_id            = $request->get('employee_id');
        $batch_id               = $request->get('batch_id');
        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'))->format("Y-m-d");
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'))->format("Y-m-d");
        $empDatepickerFromCount = Batch::whereBetween('fromDate', [$empDatepickerFrom, $empDatepickerTo])->where('id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        $empDatepickerToCount   = Batch::whereBetween('toDate', [$empDatepickerFrom, $empDatepickerTo])->where('id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        if($empDatepickerFromCount || $empDatepickerToCount)
            return 'false';

        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'));
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'));

        $work_type_id           = $request->get('work_type_id');
        $shift_id               = $request->get('shift_id');
        $status_id              = $request->get('status_id');

        $record = 0;
        $holidays = Holiday::all()->pluck('holiday_at')->toArray();
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $day_num = $i->format("N");
            if($day_num < 7 && !in_array($nowdate, $holidays)) {
                $record = 1;
            }
        }

        if($record)
            return 'false';

        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'));
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'));

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
            $data = array('department_id'=>$department_id, 'batch_id'=> $batch->id, 'employee_id'=> $employee_id, 'shift_id'=> $shift_id, 'work_type_id'=> $work_type_id, 'status_id'=> $status_id, 'leave_id'=> null, 'otHours'=> null, 'nowdate'=> $nowdate);
            $employeeRecords[] = $data;
        }
        AssignShift::insert($employeeRecords);
        return 'true';
    }

    public function checkShiftData()
	{
        return view('hr.checkShiftData');
    }
    public function importExcel(Request $request)
	{
        $validator = Validator::make($request->all(), [
            'import_file' => 'required|mimes:xlsx'
        ]);

        $fromDate      = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format('Y-m-d');
        $toDate        = Carbon::createFromFormat('d/m/Y', $request->get('toDate'))->format('Y-m-d');

        
        
        if ($validator->fails()) return redirect()->back()->withErrors($validator->errors());
		if ($request->hasFile('import_file')) {
			$path = $request->file('import_file')->getRealPath();
			$rows = \Excel::load($path, function($reader) {
			})->toArray();
			$employees = [];
			$existEmployees = [];
            $employeeIds = [];
            $punchRecords = $databaseRecords = [];
			foreach($rows as $row){
                if($row['ecode'] != null){
                    $punchRecords[$row['ecode']]['date'] =  strtotime($row['punchdate']->format('Y-m-d'));
                    $punchRecords[$row['ecode']]['shift'] = $row['shift'];
                    $punchRecords[$row['ecode']]['srno'] = $row['srno.'];
                    $punchRecords[$row['ecode']]['ecode'] = $row['ecode'];
                    $punchRecords[$row['ecode']][$punchRecords[$row['ecode']]['date']] = $row['shift'];
                }
            }
        }
        $assignShifts = AssignShift::whereBetween('nowDate', [$fromDate, $toDate])->get();
        foreach ($assignShifts as $assignShift) {
            $databaseRecords[$assignShift->employee->employee_id]['date'] = strtotime($assignShift->nowdate);
            $databaseRecords[$assignShift->employee->employee_id]['shift'] = $assignShift->shift->allias;
            $databaseRecords[$assignShift->employee->employee_id]['ecode'] = $assignShift->employee->employee_id;
            $databaseRecords[$assignShift->employee->employee_id][strtotime($assignShift->nowdate)] = $assignShift->shift->allias;
        }
        // dd($databaseRecords,$punchRecords);
        $missingDatas = $differDatas = [];
        foreach ($punchRecords as $punchRecord) {
            if(isset($databaseRecords[$punchRecord['ecode']])){
                // dd($databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']]);

                if(isset($databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']])){
                    if($databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']] != $punchRecord[$punchRecord['date']]){
                        $differDatas[] = (int)$punchRecord['srno'];
                    }
                }else{
                    $differDatas[] = (int)$punchRecord['srno'];
                }
                
            }
            else{
                $missingDatas[]=(int)$punchRecord['ecode'];
            }
            
        }
        // dd($differDatas, $missingDatas);
        return view('hr.checkShiftData', compact('missingDatas', 'differDatas'));
    }

    public function shiftDetailsShow()
    {
        $departments = Department::all(['id', 'name']);
        return view('hr.shiftDetailsShow', compact('departments')); 
    }

    public function shiftDetailPrint(Request $request)
    {
        $fromDate      = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format('Y-m-d');
        $toDate        = Carbon::createFromFormat('d/m/Y', $request->get('toDate'))->format('Y-m-d');
        
        $completedBatches = Batch::where('status', 'confirmed')->pluck('id')->toArray();
        $this->dept = $request->get('dept');  
        $employees = AssignShift::whereBetween('nowdate', [$fromDate,   $toDate])->where(function ($q) {
                    $q->where('department_id', $this->dept)
                    ->orWhere('changed_department_id', $this->dept);
                })->whereIn('batch_id', $completedBatches)->get()
                ;
        $employee_datas = [];
        foreach ($employees as $employee) {
            $employee_data['date'] = Carbon::parse($employee->nowdate)->format('d-m-Y');
            $employee_data['emp_name'] = $employee->employee->name;
            $employee_data['emp_code'] = $employee->employee->employee_id;
            if($employee->changed_department_id == 0){
                $employee_data['department_code'] = $employee->department->department_code;
                $employee_data['department_name'] = $employee->department->name;
                $employee_data['shift_code'] = $employee->shift->allias;
                $employee_data['shift_name'] = $employee->shift->name;
            }
            else{
                $employee_data['department_code'] = $employee->changed_department->department_code;
                $employee_data['department_name'] = $employee->changed_department->name;
                $employee_data['shift_code'] = $employee->changed_shift->allias;
                $employee_data['shift_name'] = $employee->changed_shift->name;
            }
            $employee_datas[] = $employee_data;
        }
        // print_r($employee_datas);die;
        return \Response::json($employee_datas);
    }

    public function changePassword()
    {
        return view('hr.changePassword');
    }
    public function admin_credential_rules(array $data)
    {
        $messages = [
            'current-password.required' => 'Please enter current password',
            'password.required' => 'Please enter password',
        ];

        $validator = Validator::make($data, [
            'current-password' => 'required',
            'password' => 'required|same:password',
            'password_confirmation' => 'required|same:password',     
        ], $messages);

        return $validator;
    } 
    public function postCredentials(Request $request)
    {
        if(Auth::Check())
        {
            $request_data = $request->All();
            $validator = $this->admin_credential_rules($request_data);
            if($validator->fails())
            {
                return redirect()->route('hr.changePassword')
                        ->withErrors($validator)
                        ->withInput();
            }
            else
            {  
                $current_password = Auth::User()->password;
                $message = '';
                if(Hash::check($request_data['current-password'], $current_password))
                {           
                    $user_id = Auth::User()->id;                       
                    $obj_user = User::find($user_id);
                    $obj_user->password = Hash::make($request_data['password']);;
                    $obj_user->save(); 
                    $message = "Password Changed Successfully";
                }
                else
                {           
                    $error = array('current-password' => 'Please enter correct current password');
                    $message = "Old Password Mismatch";                      
                }
                return redirect()->route('hr.changePassword')->with('message', $message);
            }        
        }
        else
        {
            return redirect()->to('/');
        }    
    }
   
}