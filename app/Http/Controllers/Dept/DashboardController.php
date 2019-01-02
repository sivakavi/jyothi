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
use App\Department;
use App\Batch;
use App\Leave;
use App\Holiday;
use App\ReportTemplate;

use Auth;
use Hash;
use Validator;
use Illuminate\Support\Facades\Input;

class DashboardController extends Controller
{
    private $user;

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
        $department_id = $this->user->department->id;
        $shiftDetails = [];
        $shifts = Shift::where('intime', '<', date('H:i:s'))->where('department_id', $department_id)->orderBy('intime', 'desc')->get()->toArray();
        foreach ($shifts as $key => $value) {
            $shiftDetails[] = $this->shiftdetailsformat($value, date('Y-m-d'));
        }
        $previous_shifts = Shift::where('department_id', $department_id)->orderBy('outtime', 'desc')->get()->toArray();
        $date_yesterday = date('Y-m-d',strtotime("-1 days"));
        foreach ($previous_shifts as $key => $value) {
            $shiftDetails[] = $this->shiftdetailsformat($value, $date_yesterday);
        }
        // $date_yesterday = date('Y-m-d',strtotime("-2 days"));
        // foreach ($previous_shifts as $key => $value) {
        //     $shiftDetails[] = $this->shiftdetailsformat($value, $date_yesterday);
        // }
        return view('dept.dashboard', compact('shiftDetails'));
    }

    public function shiftBatch()
    {
        $batches = Batch::where('status', 'pending')->where('department_id', $this->user->department->id)->orderBy('id', 'asc')->paginate(10);
        return view('dept.batch', compact('batches'));
    }
    
    public function holidayShiftBatch()
    {
        $batches = Batch::where('status', 'pending_holiday')->where('department_id', $this->user->department->id)->orderBy('id', 'asc')->paginate(10);
        return view('dept.holidayBatch', compact('batches'));
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
        $date = Carbon::createFromFormat('d/m/Y', $request->get('date'));
        $count = AssignShift::where('department_id', $department_id)->where('nowdate',$date)->count();
        if($count)
            return 'false';
        return 'true';
    }

    public function individualSelect(Request $request)
    {
        // $fromDate           = new \DateTime( $request->get('empDatepicker'));
        // $toDate             = new \DateTime($request->get('fromDate'));
        $fromDate      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepicker'))->format("Y-m-d");
        $toDate      = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format("Y-m-d");

        $empId              = $request->get('emp-id');
        $count              = AssignShift::where('employee_id', $empId)->whereBetween('nowdate', [$fromDate, $toDate])->get()->count();
        if($count)
            return 'false';
        return 'true';
    }

    public function bulkSelect(Request $request)
    {
        // $fromDate           = new \DateTime( $request->get('empDatepicker'));
        // $toDate             = new \DateTime($request->get('fromDate'));
        $fromDate      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepicker'))->format("Y-m-d");
        $toDate      = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format("Y-m-d");
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
        $empDatepicker          = Carbon::createFromFormat('d/m/Y', $request->get('empDatepicker'))->format("Y-m-d");
        if($request->has('fromDate')){
            $fromDate           = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format("Y-m-d");
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
        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'))->format("Y-m-d");
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'))->format("Y-m-d");
        // $empDatepickerFrom      = new \DateTime( $request->get('empDatepickerFrom'));
        // $empDatepickerTo        = new \DateTime( $request->get('empDatepickerTo'));
        $empDatepickerCount     = AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->where('employee_id', $employee_id)->get()->count();
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
            $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $employeedetail['empDatepickerFrom']);
            $empDatepickerTo      = Carbon::createFromFormat('d/m/Y', $employeedetail['empDatepickerTo']);
            $this->employeeShiftInsert($employee_id, $work_type_id, $shift_id, $status_id, $empDatepickerFrom, $empDatepickerTo);
        }
        return 'true';
    }

    public function shiftList()
    {
        $department_id = $this->user->department->id;
        // dd($department_id);
        $shifts = Shift::where('intime', '<', date('H:i:s'))->where('department_id', $department_id)->orderBy('intime', 'desc')->take(3)->get()->toArray();
        foreach ($shifts as $key => $value) {
            $shiftDetails[] = $this->shiftdetailsformat($value, date('Y-m-d'));
        }
        if(count($shifts)<3){
            $take = 3 - count($shifts);
            $previous_shifts = Shift::where('department_id', $department_id)->orderBy('outtime', 'desc')->take($take)->get()->toArray();
            foreach ($previous_shifts as $key => $value) {
                $date_yesterday = date('Y-m-d',strtotime("-1 days"));
            if($this->isWeekend($date_yesterday)){
                $date_yesterday = date('Y-m-d',strtotime("-2 days"));
            }
                $shiftDetails[] = $this->shiftdetailsformat($value, $date_yesterday);
            } 
        }
        // dd($shiftDetails);
        return view('dept.shiftList', compact('shiftDetails'));
    }

    public function shiftDetails(Request $request)
    {
        $statuses = Status::where('department_id', $this->user->department->id)->get();
        $work_types = WorkType::where('department_id', $this->user->department->id)->get();
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        $leaves = Leave::all();
        $date = $request->get('date');
        $this->shift_id = $request->get('shift_id');
        $employees = AssignShift::where('nowdate', $date)
                                ->where(function ($q) {
                                    $q->where('shift_id', $this->shift_id)
                                    ->orWhere('changed_shift_id', $this->shift_id);
                                })
                                ->where(function ($q) {
                                    $q->where('department_id', $this->user->department->id)
                                    ->orWhere('changed_department_id', $this->user->department->id);
                                })->whereNotIn('batch_id', $pendingBatches)->paginate(10)
                                ;
        $variables = ['employees' => $employees->appends(Input::except('page')),
                        'statuses' => $statuses,
                        'leaves' => $leaves,
                        'work_types' => $work_types,
                     ];
        return view('dept.shiftDetails', $variables);
    }

    public function employeeSearch(Request $request)
    {
        $emp_name = $request->get('name');
        $employees = [];
        //$employeeDetails = Employee::where('department_id', '!=', $this->user->department->id)->where('name', 'LIKE', strtolower($emp_name) . '%')->get();
        $employeeDetails = Employee::where('department_id', '!=', $this->user->department->id)->where('employee_id', $emp_name)->get();
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
            if($assignshift->employee->department->id != $this->user->department->id){
                $assignshift->ot_department_id = $this->user->department->id;;
            }
        }
        $assignshift->save();
        return 'true';

    }

    public function shiftBulkDetailsChange(Request $request)
    {
        $this->shift_id = $request->get('shift_id');
        $date      = $request->get('empDate');
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        $employees = AssignShift::where('nowdate', $date)
                                ->where(function ($q) {
                                    $q->where('shift_id', $this->shift_id)
                                    ->orWhere('changed_shift_id', $this->shift_id);
                                })
                                ->where(function ($q) {
                                    $q->where('department_id', $this->user->department->id)
                                    ->orWhere('changed_department_id', $this->user->department->id);
                                })->whereNotIn('batch_id', $pendingBatches)->pluck('id')->toArray()
                                ;
        $status_id = $request->get('status');
        $leave_id =  $request->get('leave');
        $id  = $request->get('assignShiftId');
        $othours = $request->get('othours');
        $work_type_id = $request->get('emp_work_type');
        foreach ($employees as $key => $id) {
            $assignshift = AssignShift::find($id);
            if($this->user->department->id == $assignshift->changed_department_id || $assignshift->changed_department_id == 0){
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
                    if($assignshift->employee->department->id != $this->user->department->id){
                        $assignshift->ot_department_id = $this->user->department->id;;
                    }
                }
                $assignshift->save();
            }   
        }
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
        $holidays = Holiday::all()->pluck('holiday_at')->toArray();
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
        $batch->status = 'pending';
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

    public function employeeAdd(Request $request)
    {
        $department_id = $this->user->department->id;
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
            $batch = new Batch();
            $defaultshifts = Employee::find($empId)->department->shifts->first->get()->toArray();
            $batch->department_id = $employee_details->department->id;
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

    public function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }

    public function employeeReassignList()
    {
        return view('dept.reassignlist');
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
        $department_id = $this->user->department->id;
        $shifts = Shift::where('department_id', $department_id)->get();
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        $departments = Department::all();

        $batchId = $request->get('batch_id');
        $batchDetails = Batch::find($batchId);
        $employeeShift = AssignShift::where('batch_id', $batchId)->where('employee_id', $batchDetails->employee_id)->first();
        $batches['shift_id'] = $employeeShift->shift_id;
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
        // dd($batches);
        return view('dept.reassign', compact('batches', 'shifts', 'statuses', 'work_types', 'departments'));
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
        return redirect()->route('dept.employeeReassignList');
        
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

    public function otherDept()
    {
        $statuses = Status::where('department_id', $this->user->department->id)->get();
        $work_types = WorkType::where('department_id', $this->user->department->id)->get();
        $shifts = Shift::where('department_id', $this->user->department->id)->get();

        $variables = ['statuses' => $statuses,
                        'shifts' => $shifts,
                        'work_types' => $work_types,
                     ];
        return view('dept.otherShift', $variables);
    }

    public function assignOtherDep(Request $request)
    {
        $employee_id            = $request->get('employee_id');
        // $empDatepickerFrom      = new \DateTime( $request->get('fromDate'));
        // $empDatepickerTo        = new \DateTime( $request->get('toDate'));
        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format("Y-m-d");
        $empDatepickerTo      = Carbon::createFromFormat('d/m/Y', $request->get('toDate'))->format("Y-m-d");
        $empDatepickerCount     = AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->where('employee_id', $employee_id)->get()->count();
        
        // dd(AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->where('employee_id', $employee_id)->toSql());
        if($empDatepickerCount)
            return 'false';
        else{
            
            $work_type_id           = $request->get('work_type_id');
            $shift_id               = $request->get('shift_id');
            $status_id              = $request->get('status_id');
            
            $defaultshifts = Employee::find($employee_id)->department->shifts->first->get()->toArray();

            $department_id = $this->user->department->id;
            
            $batch = new Batch();
            
            $batch->department_id = $department_id;
            $batch->employee_id = $employee_id;
            $batch->fromDate = $empDatepickerFrom;
            $batch->toDate = $empDatepickerTo;
            $batch->status = 'pending';
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
                        'shift_id'=> $defaultshifts['id'],
                        'work_type_id'=> $work_type_id,
                        'status_id'=> $status_id,
                        'nowdate'=> $nowdate,
                        'changed_department_id' => $department_id,
                        'changed_shift_id' => $shift_id
                    );
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
        

    }

    
    public function holidayShift()
    {
        $department_id = $this->user->department->id;
        $employees = Employee::where('department_id', $department_id)->get();
        $shifts = Shift::where('department_id', $department_id)->get();
        $statuses = Status::where('department_id', $department_id)->get();
        $work_types = WorkType::where('department_id', $department_id)->get();
        return view('dept.holidayShift', compact('employees', 'shifts', 'statuses', 'work_types'));
    }

    public function holidayShiftAssign(Request $request)
    {
        $employee_id            = $request->get('employee_id');
        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'));
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'));

        $work_type_id           = $request->get('work_type_id');
        $shift_id               = $request->get('shift_id');
        $status_id              = $request->get('status_id');

        $record = 0;
        $empDatepickerCount = 0;
        $empDatepickerCount     = AssignShift::whereBetween('nowdate', [$empDatepickerFrom, $empDatepickerTo])->where('employee_id', $employee_id)->get()->count();
        if($empDatepickerCount)
            return 'false';
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

        $department_id = Employee::find($employee_id)->department_id;
        $batch = new Batch();

        $empDatepickerFrom      = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerFrom'));
        $empDatepickerTo        = Carbon::createFromFormat('d/m/Y', $request->get('empDatepickerTo'));

        $batch->department_id = $department_id;
        $batch->employee_id = $employee_id;
        $batch->fromDate = $empDatepickerFrom;
        $batch->toDate = $empDatepickerTo;
        $batch->status = 'pending_holiday';
        $batch->save();

        $employeeRecords = [];
        
        for($i = $empDatepickerFrom; $i <= $empDatepickerTo; $i->modify('+1 day')){
            $nowdate =  $i->format("Y-m-d");
            $data = array('department_id'=>$department_id, 'batch_id'=> $batch->id, 'employee_id'=> $employee_id, 'shift_id'=> $shift_id, 'work_type_id'=> $work_type_id, 'status_id'=> $status_id, 'leave_id'=> null, 'otHours'=> null, 'nowdate'=> $nowdate);
            $employeeRecords[] = $data;
        }
        AssignShift::insert($employeeRecords);
        return 'true';
    }

    public function shiftDetailsShow()
    {
        return view('dept.shiftDetailsShow'); 
    }

    public function shiftDetailPrint(Request $request)
    {
        $fromDate      = Carbon::createFromFormat('d/m/Y', $request->get('fromDate'))->format('Y-m-d');
        $toDate        = Carbon::createFromFormat('d/m/Y', $request->get('toDate'))->format('Y-m-d');
        
        $completedBatches = Batch::where('status', 'confirmed')->pluck('id')->toArray();    
        $employees = AssignShift::whereBetween('nowdate', [$fromDate,   $toDate])->where(function ($q) {
                    $q->where('department_id', $this->user->department->id)
                    ->where('changed_department_id', 0)
                    ->orWhere('changed_department_id', $this->user->department->id);
                })->whereIn('batch_id', $completedBatches)->get()
                ;
        $employee_datas = [];
        foreach ($employees as $employee) {
            $employee_data['date'] = Carbon::parse($employee->nowdate)->format('d/m/Y');
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
        return view('dept.changePassword');
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
                return redirect()->route('dept.changePassword')
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
                return redirect()->route('dept.changePassword')->with('message', $message);
            }        
        }
        else
        {
            return redirect()->to('/');
        }    
    }

    public function reportPage()
    {
        $report_templates = ReportTemplate::all(['id', 'name', 'frontend_data', 'backend_data']);
        return view('dept.report', compact('report_templates'));
    }

    public function reportEmployeePage()
    {
        $department_id = $this->user->department->id;
        $employees = Employee::where('department_id',$department_id)->get();
        $report_templates = ReportTemplate::all(['id', 'name', 'frontend_data', 'backend_data']);
        return view('dept.reportEmployee', compact('employees', 'report_templates'));
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
        $pendingBatches = Batch::where('status', 'pending')->pluck('id')->toArray();
        
        $pendingBatches = implode(", ", $pendingBatches);
        $where_condition = "";
        $department_id = $this->user->department->id;
        if($request->has('employee_id')){
            $emp_id = $request->get('employee_id');
            $where_condition = "and emp.employee_id = $emp_id ";
        }else {
            $where_condition = "and emp.department_id = $department_id and emp.changed_department_id = 0 or emp.changed_department_id = $department_id ";
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

    public function assignEmpShiftAttendance()
    {
        $departments = Department::all();
        return view('dept.empAttendance', compact('departments'));
    }

    public function shiftCHDetails(Request $request)
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
        return view('dept.shiftCHDetails', $variables);
    }

    public function shiftCHDetailsChange(Request $request)
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

    public function employeeCHAdd(Request $request)
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

    public function shiftCHBulkDetailsChange(Request $request)
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

    public function employeeCHSearch(Request $request)
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

}