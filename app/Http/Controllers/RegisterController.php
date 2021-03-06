<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Models\Kiosk;
use App\Models\User;
use Auth;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kiosks = User::find(Auth::user()->id)
            ->kiosks()
            ->where('status', 1)->count();
        return view('register')
        ->with('kiosks', $kiosks);
    }
}
