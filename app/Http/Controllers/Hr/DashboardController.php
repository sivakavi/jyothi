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
        $this->department_id = $department_id;
        // $batches = Batch::where('department_id', $department_id)->where('status', 'pending')->get()->toArray();
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        $batchesIds = AssignShift::where(function ($q) {
                                    $q->where('department_id', $this->department_id)
                                    ->where('changed_department_id', 0)
                                    ->orWhere('changed_department_id', $this->department_id);
                                })->whereIn('batch_id', $pendingBatches)->pluck('batch_id')->toArray();

        $batchesIds = array_unique($batchesIds);
        $batches = Batch::whereIn('id', $batchesIds)->get()->toArray();
        
        foreach ($batches as $key => $batch) {
            $employeeShift = AssignShift::where('batch_id', $batch['id'])->where('employee_id', $batch['employee_id'])->first();

            $batches[$key]['shift_id'] = $employeeShift->shift_id;
            $batches[$key]['employee_name'] = $employeeShift->employee->name;
            $batches[$key]['category_name'] = $employeeShift->employee->category->name;
            $batches[$key]['work_type_id'] = $employeeShift->work_type_id;
            $batches[$key]['status_id'] = $employeeShift->status_id;
            $batches[$key]['employee_code'] = $employeeShift->employee->employee_id;
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
        $existRecord = AssignShift::where('batch_id', $batch->id)->first();
        AssignShift::where('batch_id', $batch->id)->delete();
        $empDatepickerFrom =  new \DateTime($empDatepickerFrom);
        $empDatepickerTo =  new \DateTime($empDatepickerTo);
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $day_num = $i->format("N");
            if($day_num < 7 && !in_array($nowdate, $holidays)) { /* weekday */
                    $data = array('department_id'=>$department_id, 'batch_id'=> $batch->id, 'employee_id'=> $employee_id, 'shift_id'=> $shift_id, 'work_type_id'=> $work_type_id, 'status_id'=> $status_id, 'leave_id'=> null, 'otHours'=> null, 'nowdate'=> $nowdate, 'changed_department_id'=> $existRecord->changed_department_id, 'changed_shift_id'=> $existRecord->changed_shift_id);
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
        $pendingBatches = Batch::where('status', 'like', 'pending%')->pluck('id')->toArray();
        $pendingBatches = implode(", ", $pendingBatches);
        $where_condition = "";
        if($request->has('employee_id')){
            $emp_id = $request->get('employee_id');
            $where_condition = "and emp.employee_id = $emp_id ";
        }
        if($pendingBatches!=""){
            $where_condition = $where_condition. "and emp.batch_id NOT IN ($pendingBatches)";
        }
        
        
        if (in_array("work_dept_name", $fieldArray)) {
            $selected[] = "CASE WHEN emp.changed_department_id=0 THEN dpt.name ELSE ch_dpt.name END as work_dept_name ";
        }
        if (in_array("work_dept_code", $fieldArray)) {
            $selected[] = "CASE WHEN emp.changed_department_id=0 THEN dpt.department_code ELSE ch_dpt.department_code END as work_dept_code";
        }
        if (in_array("shift_name", $fieldArray)) {
            $selected[] = "CASE WHEN emp.changed_shift_id=0 THEN shf.name ELSE ch_shf.name END as shift_name";
        }
        if (in_array("shift_code", $fieldArray)) {
            $selected[] = "CASE WHEN emp.changed_shift_id=0 THEN shf.allias ELSE ch_shf.allias END as shift_code";
        }
        if (in_array("shift_date", $fieldArray)) {
            $selected[] = "DATE_FORMAT(emp.nowdate, '%d/%m/%Y') as shift_date";
        }
        if (in_array("status", $fieldArray)) {
            $selected[] = "st.name as status";
        }
        if (in_array("process", $fieldArray)) {
            $selected[] = "wt.name as process";
        }
        if (in_array("leave_type", $fieldArray)) {
            $selected[] = "CASE WHEN emp.leave_id IS NULL THEN '' ELSE lv.name END as leave_type";
        }
        if (in_array("ot_hours", $fieldArray)) {
            $selected[] = "CASE WHEN emp.otHours IS NULL THEN '0' else emp.otHours END as ot_hours";
        }
        if (in_array("ot_department", $fieldArray)) {
            $selected[] = "CASE WHEN emp.otHours IS NULL THEN '' else CASE WHEN emp.ot_department_id=0 THEN dpt.name ELSE ot_dpt.name END END as ot_department";
        }
        if (in_array("emp_name", $fieldArray)) {
            $selected[] = "usr.name as emp_name";
        }
        if (in_array("emp_dept_name", $fieldArray)) {
            $selected[] = "emp_dpt.name as emp_dept_name";
        }
        if (in_array("emp_dep_code", $fieldArray)) {
            $selected[] = "emp_dpt.department_code as emp_dep_code";
        }
        if (in_array("emp_code", $fieldArray)) {
            $selected[] = "usr.employee_id as emp_code";
        }
        if (in_array("cost_centre", $fieldArray)) {
            $selected[] = "el.cost_centre as cost_centre";
        }
        if (in_array("cost_centre_desc", $fieldArray)) {
            $selected[] = "usr.cost_centre_desc as cost_centre_desc";
        }
        if (in_array("cost_centre_desc", $fieldArray)) {
            $selected[] = "usr.cost_centre_desc as cost_centre_desc";
        }
        if (in_array("gl_account", $fieldArray)) {
            $selected[] = "el.gl_accounts as gl_account";
        }
        if (in_array("gl_account_desc", $fieldArray)) {
            $selected[] = "usr.gl_description as gl_account_desc";
        }
        if (in_array("location", $fieldArray)) {
            $selected[] = "loc.name as location";
        }
        if (in_array("category", $fieldArray)) {
            $selected[] = "cat.name as category";
        }
        if (in_array("gender", $fieldArray)) {
            $selected[] = "usr.gender as gender";
        }
        $select_query = implode(", ", $selected);
        $finalArray = [];
        $finalArray = \DB::select("SELECT 
        $select_query
        from assign_shifts emp  
        INNER JOIN departments dpt on dpt.id = emp.department_id 
        INNER JOIN departments ch_dpt on ch_dpt.id = emp.changed_department_id 
        INNER JOIN shifts shf on shf.id = emp.shift_id 
        INNER JOIN shifts ch_shf on ch_shf.id = emp.changed_shift_id
        INNER JOIN statuses st on st.id = emp.status_id 
        INNER JOIN work_types wt on wt.id = emp.work_type_id 
        LEFT JOIN leaves lv on lv.id = emp.leave_id 
        LEFT JOIN departments ot_dpt on ot_dpt.id = emp.ot_department_id
        INNER JOIN employees usr on usr.id = emp.employee_id 
        INNER JOIN employee_logs el on el.employee_id = emp.employee_id 
        INNER JOIN departments emp_dpt on emp_dpt.id = el.department_id
        INNER JOIN categories cat on cat.id = el.category_id 
        INNER JOIN locations loc on loc.id = el.location_id 
        
        where el.created_at = 
            (select created_at 
            from employee_logs 
            where created_at <= CONCAT(emp.nowdate, ' 23:59:59')  
            and employee_id = emp.employee_id 
            ORDER BY created_at DESC limit 1) 
            $where_condition and emp.nowdate between ? and ?", [$fromDate, $toDate])
        ;
        
        // $finalArray = [];
        //$finalArray[] = $upperCaseFieldArray;

        

        return $finalArray;
    }

    public function holidayBatch()
    {
        $holiday_department_count = [];
        $batches = Batch::groupBy('department_id')->paginate(10);
        $departments = Department::all()->pluck('id')->toArray();
        foreach($departments as $department_id){
            $holiday_department_count[$department_id] = Batch::where('department_id', $department_id)->where('status', 'pending_holiday')->get()->count();
        }
        return view('hr.holidayBatch', compact('batches', 'holiday_department_count'));
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
            $batches[$key]['employee_code'] = $employeeShift->employee->employee_id;
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
        $empDatepickerFromCount = AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->where('batch_id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        $empDatepickerToCount   = AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->where('batch_id', '!=', $batch_id)->where('employee_id', $employee_id)->get()->count();
        
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
                    $punchRecords[$row['ecode']][$punchRecords[$row['ecode']]['date']]['shift'] = $row['shift'];
                    $punchRecords[$row['ecode']][$punchRecords[$row['ecode']]['date']]['status'] = $row['mustermark'];
                }
            }
        }
        if(count($punchRecords) == 0){
            $validator->errors()->add('import_file', 'Excel is empty');
            return redirect()->back()->withErrors($validator->errors());
        }
        
        $assignShifts = AssignShift::whereBetween('nowDate', [$fromDate, $toDate])->get();
        foreach ($assignShifts as $assignShift) {
            $databaseRecords[$assignShift->employee->employee_id]['date'] = strtotime($assignShift->nowdate);
            $databaseRecords[$assignShift->employee->employee_id]['shift'] = $assignShift->shift->allias;
            $databaseRecords[$assignShift->employee->employee_id]['ecode'] = $assignShift->employee->employee_id;
            $databaseRecords[$assignShift->employee->employee_id][strtotime($assignShift->nowdate)]['shift'] = $assignShift->shift->allias;
            if($assignShift->changed_shift_id != 0){
                $databaseRecords[$assignShift->employee->employee_id][strtotime($assignShift->nowdate)]['shift'] = $assignShift->changed_shift->allias;
            }
            $databaseRecords[$assignShift->employee->employee_id][strtotime($assignShift->nowdate)]['status'] = $assignShift->status->name;
        }
        // dd($databaseRecords,$punchRecords);
        $missingDatas = $differDatas = [];
        foreach ($punchRecords as $punchRecord) {
            if(isset($databaseRecords[$punchRecord['ecode']])){
                // dd($databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']]);

                if(isset($databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']])){
                    if($databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']]['shift'] != $punchRecord[$punchRecord['date']]['shift'] || $databaseRecords[$punchRecord['ecode']][$databaseRecords[$punchRecord['ecode']]['date']]['status'] != $punchRecord[$punchRecord['date']]['status']){
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
        return view('hr.checkShiftDataResult', compact('missingDatas', 'differDatas'));
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
                    ->where('changed_department_id', 0)
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

    public function employeeReassignList()
    {
        return view('hr.reassignlist');
    }
    
    public function employeeBatchSearch(Request $request)
    {
        $emp_name = $request->get('name');
        $employee = $batches = [];
        //$employee = Employee::where('department_id', $this->user->department->id)->where('name', strtolower($emp_name))->pluck('id')->toArray();
        $employee = Employee::where('employee_id', $emp_name)->pluck('id')->toArray();
        if(count($employee)){
            $batches = Batch::where('employee_id', $employee[0])->where('toDate','>', new \DateTime())->orderBy('created_at', 'DESC')->take(3)->get()->toArray();
        }
        else{
            return '';
        }
        if(count($batches))
            return \Response::json($batches);
        return '';
    }

    public function getWorkType(Request $request)
    {
        $department_id = $request->input('department_id');
        $shifts = WorkType::all(['id', 'name', 'department_id'])->where('department_id',$department_id);
        $data = [];
        foreach($shifts as $shift){
            $data[$shift->id] = $shift->name;
        }
        return response()->json($data);
    }

    public function getStatus(Request $request)
    {
        $department_id = $request->input('department_id');
        $shifts = Status::all(['id', 'name', 'department_id'])->where('department_id',$department_id);
        $data = [];
        foreach($shifts as $shift){
            $data[$shift->id] = $shift->name;
        }
        return response()->json($data);
    }

    public function employeeReassign(Request $request)
    {
        $batchId = $request->get('batch_id');
        $batchDetails = Batch::find($batchId);
        $employeeShift = AssignShift::where('batch_id', $batchId)->where('employee_id', $batchDetails->employee_id)->first();
        $department_id = $employeeShift->changed_department_id;
        if($department_id == 0){
            $department_id = $employeeShift->department_id;
        }
        
        $shifts = Shift::where('department_id', $department_id)->get();
        // dd($shifts->toArray());
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        $departments = Department::all();

        $batchId = $request->get('batch_id');
        $batchDetails = Batch::find($batchId);
        $employeeShift = AssignShift::where('batch_id', $batchId)->where('employee_id', $batchDetails->employee_id)->first();
        $batches['shift_id'] = $employeeShift->shift_id;
        $batches['changed_shift_id'] = $employeeShift->changed_shift_id;
        $batches['employee_name'] = $employeeShift->employee->name;
        $batches['category_name'] = $employeeShift->employee->category->name;
        $batches['work_type_id'] = $employeeShift->work_type_id;
        $batches['status_id'] = $employeeShift->status_id;
        $batches['fromDate'] = $batchDetails->fromDate;
        $batches['toDate'] = $batchDetails->toDate;
        $batches['check'] = true;
        if(new \DateTime($batches['fromDate']) > new \DateTime()){
            $batches['check'] = false;
        }
        return view('hr.reassign', compact('batches', 'shifts', 'statuses', 'work_types', 'departments', 'department_id'));
            ;
    }

    public function employeeReassignStore(Request $request)
    {
        $batch_id = $request->get('batch_id');
        $fromDate = $request->get('fromDate');
        $toDate = $request->get('toDate');
        $status_id = $request->get('status_id');
        $department_id = $request->get('department_id');
        $shift_id = $request->get('shift_id');
        $work_type_id = $request->get('work_type_id');
        $batchDetails = Batch::find($batch_id);
        //$fromDate      = Carbon::createFromFormat('d/m/Y', $fromDate);
        //$toDate      = Carbon::createFromFormat('d/m/Y', $toDate);
        $fromDate = new \DateTime($fromDate);
        $toDate = new \DateTime($toDate);
        
        $employee_id = $batchDetails->employee_id;
        if(new \DateTime($batchDetails->fromDate) > new \DateTime()){
            $this->employeeReAssignShiftInsert($department_id, $employee_id, $work_type_id, $shift_id, $status_id, $request->get('fromDate'), $request->get('toDate'));
            $batchDetails->delete();
            AssignShift::where('batch_id', $batch_id)->delete();
        }
        else{
            $previous_day = $fromDate->modify('-1 day');
            $batchDetails->toDate = $previous_day;
            $batchDetails->save();
            $fromDate->modify('+1 day');
            $this->employeeReAssignShiftInsert($department_id, $employee_id, $work_type_id, $shift_id, $status_id, $request->get('fromDate'), $request->get('toDate'));
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            //$fromDate      = Carbon::createFromFormat('d/m/Y', $fromDate)->format("Y-m-d");
            //$toDate      = Carbon::createFromFormat('d/m/Y', $toDate)->format("Y-m-d");
            $fromDate = new \DateTime($fromDate);
            $toDate = new \DateTime($toDate);
            $deleteShifts = AssignShift::where('batch_id', $batch_id)->whereBetween('nowdate', [$fromDate, $toDate]);     
            $deleteShifts->delete();
        }
        return redirect()->route('hr.employeeReassignList');
        
    }

    private function employeeReAssignShiftInsert($ch_department_id, $employee_id, $work_type_id, $ch_shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo)
    {
        
        $defaultshifts = Employee::find($employee_id)->department->shifts->first->get()->toArray();

        $ass_shift_id = $ch_shift_id;
        // echo $ass_shift_id;
        $change_department_id = $change_shift_id = 0;
        if($defaultshifts['department_id'] != $ch_department_id){
            $change_department_id = $ch_department_id;
            $change_shift_id = $ch_shift_id;
            $ass_shift_id = $defaultshifts['id'];
        }
        // dd($defaultshifts['department_id'] != $ch_department_id);
        // dd($defaultshifts['department_id'],$ass_shift_id,$change_department_id,$change_shift_id,$ch_department_id,$ch_shift_id);
        $batch = new Batch();

        $batch->department_id = $defaultshifts['department_id'];
        $batch->employee_id = $employee_id;
        $batch->fromDate = $empDatepickerFrom;
        $batch->toDate = $empDatepickerTo;
        $batch->status = 'confirmed';
        $batch->save();

        $employeeRecords = [];
        $holidays = Holiday::all()->pluck('holiday_at')->toArray();
        $empDatepickerFrom =  new \DateTime($empDatepickerFrom);
        $empDatepickerTo =  new \DateTime($empDatepickerTo);
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $day_num = $i->format("N");
            if($day_num < 7 && !in_array($nowdate, $holidays)) { /* weekday */
                    $data = array(
                    'department_id'=>$defaultshifts['department_id'],
                    'batch_id'=> $batch->id,
                    'employee_id'=> $employee_id, 
                    'shift_id'=> $ass_shift_id, 
                    'work_type_id'=> $work_type_id, 
                    'status_id'=> $status_id, 
                    'leave_id'=> null, 
                    'otHours'=> null, 
                    'nowdate'=> $nowdate, 
                    'changed_department_id'=> $change_department_id, 
                    'changed_shift_id'=> $change_shift_id);
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