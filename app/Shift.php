<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Shift extends Model
{

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('id', function (Builder $builder) {
            $builder->where('id', '!=', 0);
        });
    }

    public function department()
    {
    	return $this->belongsTo('App\Department');
    }
}
