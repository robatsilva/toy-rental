<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toy extends Model
{
    public function kiosk()
    {
        return $this->belongsTo('App\Models\Kiosk');
    }

    public function rental()
    {
        return $this->hasOne('App\Models\Rental');
    }
}
