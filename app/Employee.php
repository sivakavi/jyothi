<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\EmployeeCreated;
use App\Events\EmployeeSaved;

class Employee extends Model
{
    protected $events = [
        'saved' => EmployeeSaved::class,
        'created' => EmployeeCreated::class,
    ];
    //
    public function department()
    {
    	return $this->belongsTo('App\Department');
    }

    public function category()
    {
    	return $this->belongsTo('App\Category');
    }

    public function location()
    {
    	return $this->belongsTo('App\Location');
    }

    public function employeeLogs()
    {
    	return $this->hasMany('App\EmployeeLog');
    }

    public function employeeLogsLatestUpdate($nowdate)
    {
        $instance = $this->employeeLogs();
        return $instance->where('created_at', '<', $nowdate)->orderBy('created_at', 'DESC')->first();
    }
}
