<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeLog extends Model
{
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

    public function employee()
    {
    	return $this->belongsTo('App\Employee');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
    
}
