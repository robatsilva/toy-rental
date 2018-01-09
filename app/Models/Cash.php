<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    public function employe()
    {
        return $this->belongsTo('App\Models\Employe');
    }
}
