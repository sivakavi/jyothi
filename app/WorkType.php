<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
    //
    public function department()
    {
    	return $this->belongsTo('App\Department');
    }
}
