<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    public function employe()
    {
        return $this->belongsTo('App\Models\Employe');
    }

    public function cash_drawer()
    {
        return $this->belongsTo('App\Models\CashDrawer');
    }
}
