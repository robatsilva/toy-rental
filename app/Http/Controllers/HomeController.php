<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use Session;
use Auth;

use App\Models\Toy;
use App\Models\Kiosk;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('web.home');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $user = User::find(Auth::user()->id);
        if($user->kiosk_id)
            $kiosk_id = $user->kiosk_id;
        else{
            $kiosk_id = Kiosk::
            where('user_id', $user->id)
            ->where('default', "1")
            ->first()->id;
        }
            dd($kiosk_id);
        return view('rental.list')->with('kiosk_id', $kiosk_id);
    }
}
