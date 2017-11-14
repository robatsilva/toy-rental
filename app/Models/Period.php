<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    public function kiosk()
    {
        return $this->belongsTo('App\Models\Kiosk');
    }
}
