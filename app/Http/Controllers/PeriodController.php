<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use DB;

use App\Models\Period;
use App\Models\Kiosk;
use App\Models\User;

class PeriodController extends Controller
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

    
    public function index()
    {
        $periods = Period::selectRaw('periods.*, periods.status as status_period')
        ->join('kiosk_user', 'kiosk_user.kiosk_id', '=', 'periods.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosk_user.user_id')
        ->where('users.id', Auth::user()->id)
        ->get();
        return view('periods.list')->with('periods', $periods);
    }

    public function listPeriods(){
        $periods = Period::select('periods.*')
        ->join('kiosks', 'kiosks.id', '=', 'periods.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosks.user_id')
        ->where('users.id', Auth::user()->id)
        ->get();
        return response()->json($periods);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByKioskId($kiosk_id)
    {
        SecurityCheckController::securityCheck($kiosk_id);

        $periods = Period::where('kiosk_id', $kiosk_id)
        ->where('status', 1)
        -orderBy('time')
        ->get();
        return response()->json($periods);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kiosks = User::find(Auth::user()->id)
                ->kiosks()
                ->where('status', 1)->get();
        return view('periods/form')
            ->with('period', null)
            ->with('kiosks', $kiosks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first()) {
            SecurityCheckController::securityCheck($request->input('kiosk_id'));

            $period = new Period;
            $period->time = $request->input('time');
            $period->value = $request->input('value');
            $period->kiosk_id = $request->input('kiosk_id');
            $period->save();
        }
        return redirect('period');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $period = Period::find($id);

        SecurityCheckController::securityCheck($period->kiosk_id);
        $kiosks = User::find(Auth::user()->id)
                ->kiosks()
                ->where('status', 1)->get();
        return view('periods.form')
            ->with('kiosks', $kiosks)
            ->with("period", $period);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first()) {
            $period = Period::find($id);
            SecurityCheckController::securityCheck($period->kiosk_id);

            $period->time = $request->input('time');
            $period->value = $request->input('value');
            $period->kiosk_id = $request->input('kiosk_id');
            $period->save();
        }
        return redirect('period');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toogle($id)
    {
        if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first()) {
            $period = Period::find($id);
            SecurityCheckController::securityCheck($period->kiosk_id);
            
            if($period->status)
                $period->status = 0;
            else
                $period->status = 1;
            $period->save();
        }
        return redirect('period');
    }
}
