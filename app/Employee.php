<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\EmployeeCreated;
use App\Events\EmployeeSaved;
use Illuminate\Database\Eloquent\Builder;
use Kyslik\ColumnSortable\Sortable;

class Employee extends Model
{

    use Sortable;

    public $sortable = ['name','employee_id'];

    protected $events = [
        'saved' => EmployeeSaved::class,
        'created' => EmployeeCreated::class,
    ];
    //

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('remark', function (Builder $builder) {
            $builder->where('remark', 'active');
        });
    }

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
