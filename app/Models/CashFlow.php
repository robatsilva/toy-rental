<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    public function employe()
    {
        return $this->belongsTo('App\Models\Employe');
    }
}
