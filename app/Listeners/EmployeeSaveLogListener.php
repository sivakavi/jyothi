<?php

namespace App\Listeners;

use App\Events\EmployeeSaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\EmployeeLog;
use Illuminate\Support\Facades\Auth;

class EmployeeSaveLogListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmployeeSaved  $event
     * @return void
     */
    public function handle(EmployeeSaved $event)
    {
        //
        $employee = $event->employee;
        $original = $employee->getOriginal();
        if(!empty($original) && ((int)$employee->department_id != $original['department_id'] || (int)$employee->category_id != $original['category_id'] || (int)$employee->location_id != $original['location_id'] || $employee->gl_accounts != $original['gl_accounts'] || $employee->cost_centre != $original['cost_centre'])) {
            $employeeLog = new EmployeeLog();
            $employeeLog->employee_id = $employee->id;
            $employeeLog->action = 'Update';  
            $employeeLog->user_id = Auth::user()->id;
            $employeeLog->department_id = $employee->department_id;
            $employeeLog->category_id = $employee->category_id;
            $employeeLog->location_id = $employee->location_id;
            $employeeLog->cost_centre = $employee->cost_centre;
            $employeeLog->gl_accounts = $employee->gl_accounts;
            $employeeLog->save();
        }
    }
}
