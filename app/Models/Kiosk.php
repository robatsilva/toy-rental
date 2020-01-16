<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kiosk extends Model
{
    public function users()
    {
        return $this->belongsToMany('App\Models\User');
    }

    public function employe()
    {
        return $this->hasMany('App\Models\User', 'kiosk_id');
    }

    public function customers()
    {
        return $this->hasMany('App\Customer');
    }

    public function rentals()
    {
        return $this->hasMany('App\Rental');
    }

    public function toys()
    {
        return $this->hasMany('App\Toy');
    }
    
    public function types()
    {
        return $this->belongsToMany('App\Models\Type');
    }
}
