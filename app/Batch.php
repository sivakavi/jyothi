<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    public function department()
    {
    	return $this->belongsTo('App\Department');
    }

    public function employee()
    {
    	return $this->belongsTo('App\Employee');
    }
}
