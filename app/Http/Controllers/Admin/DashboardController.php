<?php

namespace App\Http\Controllers\Admin;

use App\Models\Auth\User\User;
use Arcanedev\LogViewer\Entities\Log;
use Arcanedev\LogViewer\Entities\LogEntry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Route;
use App\College;
use App\Category;
use App\Group;
use App\Employee;
use App\Shift;
use App\Department;
use App\Leave;
use App\Status;
use App\WorkType;
use App\AssignShift;
use App\Batch;
use App\ReportTemplate;
use Auth;
use Illuminate\Support\Facades\Input;
use Hash;
use Validator;

class DashboardController extends Controller
{

    private $department_id;

    private $shift_id;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::all()->count();
        $shifts = Shift::all()->count();
        $users = User::all()->count();
        $employees = Employee::all()->count();
        $shift_ids = Shift::where('intime', '<', date('H:i:s'))->pluck('id')->toArray();
        $nowdate = new \DateTime();
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        // $nowdate->modify('-1 day');
        $assignShifts = AssignShift::whereIn('shift_id', $shift_ids)
                        ->where('nowdate', $nowdate->format('Y-m-d'))
                        ->whereNotIn('batch_id', $pendingBatches)
                        ->get();
                        // dd($assignShifts);
        $departmentDatas = [];
        foreach($assignShifts as $assignShift){
            if($assignShift->changed_department_id == '0'){
                if($assignShift->status->name != 'LL' && $assignShift->status->name != 'AA'){
                    $departmentDatas[$assignShift->department_id]['name'] = $assignShift->department->name;
                    if(!isset($departmentDatas[$assignShift->department_id]['present'])){
                        $departmentDatas[$assignShift->department_id]['present'] = 0;
                    }
                    $departmentDatas[$assignShift->department_id]['present'] = $departmentDatas[$assignShift->department_id]['present'] + 1;
                    $departmentDatas[$assignShift->department_id]['department_id'] = $assignShift->department_id;
                    if(!isset($departmentDatas[$assignShift->department_id][$assignShift->work_type->name])){
                        $departmentDatas[$assignShift->department_id][$assignShift->work_type->name] = 0;
                    }
                    $departmentDatas[$assignShift->department_id][$assignShift->work_type->name] = $departmentDatas[$assignShift->department_id][$assignShift->work_type->name] + 1;
                }
                else{
                    $departmentDatas[$assignShift->department_id]['name'] = $assignShift->department->name;
                    if(!isset($departmentDatas[$assignShift->department_id]['absent'])){
                        $departmentDatas[$assignShift->department_id]['absent'] = 0;
                    }
                    $departmentDatas[$assignShift->department_id]['absent'] = $departmentDatas[$assignShift->department_id]['absent'] + 1;
                    $departmentDatas[$assignShift->department_id]['department_id'] = $assignShift->department_id;
                }
            }
            else{
                if($assignShift->status->name != 'LL' && $assignShift->status->name != 'AA'){
                    $departmentDatas[$assignShift->changed_department_id]['name'] = $assignShift->changed_department->name;
                    if(!isset($departmentDatas[$assignShift->changed_department_id]['present'])){
                        $departmentDatas[$assignShift->changed_department_id]['present'] = 0;
                    }
                    $departmentDatas[$assignShift->changed_department_id]['present'] = $departmentDatas[$assignShift->changed_department_id]['present'] + 1;
                    $departmentDatas[$assignShift->changed_department_id]['department_id'] = $assignShift->changed_department_id;
                    if(!isset($departmentDatas[$assignShift->changed_department_id][$assignShift->work_type->name])){
                        $departmentDatas[$assignShift->changed_department_id][$assignShift->work_type->name] = 0;
                    }
                    $departmentDatas[$assignShift->changed_department_id][$assignShift->work_type->name] = $departmentDatas[$assignShift->changed_department_id][$assignShift->work_type->name] + 1;
                }
                else{
                    $departmentDatas[$assignShift->changed_department_id]['name'] = $assignShift->changed_department->name;
                    if(!isset($departmentDatas[$assignShift->changed_department_id]['absent'])){
                        $departmentDatas[$assignShift->changed_department_id]['absent'] = 0;
                    }
                    $departmentDatas[$assignShift->changed_department_id]['absent'] = $departmentDatas[$assignShift->changed_department_id]['absent'] + 1;
                    $departmentDatas[$assignShift->changed_department_id]['department_id'] = $assignShift->changed_department_id;
                }
            }
        }
        $today = $nowdate->format('d/m/Y');
        return view('admin.dashboard',  compact('departments', 'shifts', 'users', 'employees', 'departmentDatas', 'today'));
    }

    public function getDepartmentEmployeeAttendance(Request $request)
    {
        $department_id = $request->input('department_id');
        $shift_ids = Shift::where('intime', '<', date('H:i:s'))->pluck('id')->toArray();
        $this->department_id = $department_id;
        $nowdate = new \DateTime();
        // $nowdate->modify('-1 day');
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        $assignShifts = AssignShift::whereIn('shift_id', $shift_ids)
                                    ->where(function ($q) {
                                        $q->where('department_id', $this->department_id)
                                        ->where('changed_department_id',0)
                                        ->orWhere('changed_department_id', $this->department_id);
                                    })
                                    ->whereNotIn('batch_id', $pendingBatches)
                                    ->where('nowdate', $nowdate->format('Y-m-d'))
                                    ->get();
        $departmentDatas = [];
        
        // dd($pendingBatches);
        // dd($assignShifts);
        $present = $absent = [];
        foreach($assignShifts as $assignShift){
            if($assignShift->changed_department_id == '0'){
                // echo $assignShift->status->name;
                if($assignShift->status->name != 'LL' && $assignShift->status->name != 'AA'){
                    $present[] = $assignShift->employee_id;
                }
                else{
                    $absent[] = $assignShift->employee_id;
                }
            }
            else{
                if($assignShift->status->name != 'LL' && $assignShift->status->name != 'AA'){
                    $present[] = $assignShift->employee_id;
                }
                else{
                    $absent[] = $assignShift->employee_id;
                }
            }
        }
        $presentEmployees = Employee::whereIn('id', $present)->get();
        $absentEmployees = Employee::whereIn('id', $absent)->get();
        $today = $nowdate->format('d/m/Y');
        $department = Department::find($department_id);
        $departmentName = $department->name;
        $departmentCode = $department->department_code;
        // dd($present, $absent);
        return view('admin.empAttendanceDetails', compact('presentEmployees', 'absentEmployees', 'today', 'departmentName', 'departmentCode'));        
    }

    public function assignEmpShiftAttendance()
    {
        $departments = Department::all();
        return view('admin.empAttendance', compact('departments'));
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
        $leaves = Leave::all();
        $work_types = WorkType::where('department_id', $department_id)->get();
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
        return view('admin.shiftDetails', $variables);
    }

    public function employeeSearch(Request $request)
    {
        $department_id = $request->get('department_id');
        $emp_name = $request->get('name');
        $employees = [];
        //$employeeDetails = Employee::where('department_id', '!=', $department_id)->where('name', 'LIKE', strtolower($emp_name) . '%')->get();
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
        $assignshift = AssignShift::find($id);

        if(Status::find($status_id)->name != 'OT'){
            $assignshift->status_id = $status_id;
        }
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

    public function changePassword()
    {
        return view('admin.changePassword');
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
                return redirect()->route('admin.changePassword')
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
                return redirect()->route('admin.changePassword')->with('message', $message);
            }        
        }
        else
        {
            return redirect()->to('/');
        }    
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
        return view('admin.report', compact('report_templates'));
    }

    public function reportEmployeePage()
    {
        $departments = Department::all(['id', 'name']);
        $report_templates = ReportTemplate::all(['id', 'name', 'frontend_data', 'backend_data']);
        return view('admin.reportEmployee', compact('departments', 'report_templates'));
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
                $nowdate=new \DateTime($singleRow->nowdate);
                $singleItem["shift_date"] = $nowdate->format('d/m/Y');
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



}
