<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Rental extends Model
{
    public function kiosk()
    {
        return $this->belongsTo('App\Models\Kiosk');
    }

    public function toy()
    {
        return $this->belongsTo('App\Models\Toy');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer');
    }

    public function period()
    {
        return $this->belongsTo('App\Models\Period');
    }
/*
    public function getInitAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('d/m/Y h:i');
    }
    public function getEndAttribute($value) {
        if($value)
            return \Carbon\Carbon::parse($value)->format('d/m/Y h:i');
    }
  */  
}
