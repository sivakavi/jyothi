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
use Auth;
use Illuminate\Support\Facades\Input;
use Hash;
use Validator;

class DashboardController extends Controller
{
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
        $assignShifts = AssignShift::whereIn('shift_id', [5,3])->get();
        $departmentDatas = [];
        foreach($assignShifts as $assignShift){
            if($assignShift->changed_department_id == '0'){
                if($assignShift->status->name != 'Leave'){
                    $departmentDatas[$assignShift->department_id]['name'] = $assignShift->department->name;
                    if(!isset($departmentDatas[$assignShift->department_id]['present'])){
                        $departmentDatas[$assignShift->department_id]['present'] = 0;
                    }
                    $departmentDatas[$assignShift->department_id]['present'] = $departmentDatas[$assignShift->department_id]['present'] + 1;
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
                }
            }
            else{
                if($assignShift->status->name != 'Leave'){
                    $departmentDatas[$assignShift->changed_department_id]['name'] = $assignShift->changed_department->name;
                    if(!isset($departmentDatas[$assignShift->changed_department_id]['present'])){
                        $departmentDatas[$assignShift->changed_department_id]['present'] = 0;
                    }
                    $departmentDatas[$assignShift->changed_department_id]['present'] = $departmentDatas[$assignShift->changed_department_id]['present'] + 1;
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
                }
            }
        }
        return view('admin.dashboard',  compact('departments', 'shifts', 'users', 'employees', 'departmentDatas'));
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
        $statuses = Status::where('department_id', $department_id)->get();
        $leaves = Leave::all();
        $date = $request->get('date');
        $shift_id = $request->get('shift_id');
        $employees = AssignShift::where('nowdate', $date)
                                ->where('shift_id', $shift_id)
                                ->where(function ($q) {
                                    $q->where('department_id', $this->department_id)
                                    ->orWhere('changed_department_id', $this->department_id);
                                })->paginate(10)
                                ;
        $variables = ['employees' => $employees->appends(Input::except('page')),
                        'statuses' => $statuses,
                        'leaves' => $leaves,
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

    public function employeeAdd(Request $request)
    {
        $empId = $request->get('empId');
        $department_id = $request->get('department_id');
        $empDate = new \DateTime($request->get('empDate'));
        $emp = AssignShift::where('employee_id', $empId)->where('nowdate', $empDate)->first();
        if($emp){
            $emp->changed_department_id = $department_id;
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



}
