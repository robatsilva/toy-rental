<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $table = 'customers';
    public function kiosk()
    {
        return $this->belongsTo('App\Kiosk');
    }
    
}
