<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
    
    public function shifts()
    {
        return $this->hasMany('App\Shift');
    }
}
