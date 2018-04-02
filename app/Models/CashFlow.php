<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    public function employe()
    {
        return $this->belongsTo('App\Models\Employe');
    }
}
