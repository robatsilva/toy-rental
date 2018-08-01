<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToyLog extends Model
{
    public function toy()
    {
        return $this->hasOne('App\Models\Toy');
    }
}
