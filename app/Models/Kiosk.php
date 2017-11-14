<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kiosk extends Model
{
    public function user()
    {
        return $this->belongsTo('App\user');
    }

    public function customers()
    {
        return $this->hasMany('App\Csutomer');
    }

    public function rentals()
    {
        return $this->hasMany('App\Rental');
    }

    public function toys()
    {
        return $this->hasMany('App\Toy');
    }
}
