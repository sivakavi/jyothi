<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    public function department()
    {
    	return $this->belongsTo('App\Department');
    }
}
