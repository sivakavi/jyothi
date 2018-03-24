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
        $batches = Batch::groupBy('department_id')->paginate(10);
        return view('hr.batch', compact('batches'));
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
   
}