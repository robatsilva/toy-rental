<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Employe extends Authenticatable
{
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function kiosk()
    {
        return $this->belongsTo('App\Models\Kiosk');
    }

    public function kiosks()
    {
        return $this->belongsToMany('App\Models\Kiosk')->withPivot('default');
    }

    public function permissions()
    {
        return $this->belongsToMany('App\Models\Permission', 'permission_user', 'user_id', 'permission_id');
    }

}
