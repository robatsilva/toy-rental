<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;

use App\Models\Rental;
use App\Models\Customers;
use App\Models\Toy;
use App\Models\Kiosk;
use App\Models\Period;
use App\Models\Employe;
use App\User;
use App\Models\Reason;
use App\Models\Cash;

class SecurityCheckController extends Controller
{
    public static function securityCheck($kiosk_id){
        $user = User::find(Auth::user()->id);

        // Se for funcionário
        if($user->kiosk_id){
            if($user->kiosk_id != $kiosk_id)
                Self::unauthorized();

        } else {
            // Se não é funcionário, verifica se tem o kisosk no relacionamento
            $kiosk_user = Kiosk::has('users')
                ->where('id', $kiosk_id)
                ->get();
            
            if($kiosk_user->isEmpty()){
                Self::unauthorized();
            }
        }
    }

    public static function securityCheckByRental($id){
        $rental = Rental::find($id);
        Self::securityCheck($rental->kiosk_id);
    }

    public static function unauthorized(){
        dd('Ooops...., o que você está tentando fazer? Parece que você não pode fazer isso!!!!');
    }
}
