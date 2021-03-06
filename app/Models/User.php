<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'birth', 'cnpj', 'area_code', 'phone', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function kiosks()
    {
        return $this->belongsToMany('App\Models\Kiosk')->withPivot('default');
    }
    
    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission');
    }
}
