<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignShift extends Model
{
    //
    public function employee()
    {
    	return $this->belongsTo('App\Employee');
    }

    public function shift()
    {
    	return $this->belongsTo('App\Shift');
    }

    public function status()
    {
    	return $this->belongsTo('App\Status');
    }

    public function work_type()
    {
    	return $this->belongsTo('App\WorkType');
    }

    public function leave()
    {
        return $this->belongsTo('App\Leave');
    }

    public function changed_department()
    {
        return $this->belongsTo('App\Department', 'changed_department_id');
    }
}
