<?php

namespace App\Listeners;

use App\Events\EmployeeCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\EmployeeLog;
use Illuminate\Support\Facades\Auth;


class EmployeeCreateLogListener
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
     * @param  EmployeeCreated  $event
     * @return void
     */
    public function handle(EmployeeCreated $event)
    {
        $employee = $event->employee;
        $employeeLog = new EmployeeLog();
        $employeeLog->employee_id = $employee->id;
		$employeeLog->action = 'Insert';    
		$employeeLog->user_id = Auth::user()->id;
		$employeeLog->department_id = $employee->department_id;
		$employeeLog->category_id = $employee->category_id;
		$employeeLog->location_id = $employee->location_id;
		$employeeLog->cost_centre = $employee->cost_centre;
        $employeeLog->gl_accounts = $employee->gl_accounts;
        $employeeLog->save();
    }
}
