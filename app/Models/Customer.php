<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public function kiosk()
    {
        return $this->belongsTo('App\Kiosk');
    }
}
