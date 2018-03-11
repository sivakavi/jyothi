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
}
