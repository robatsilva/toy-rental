<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use DB;

use App\Models\Period;
use App\Models\Kiosk;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = Period::select('periods.*')
        ->join('kiosks', 'kiosks.id', '=', 'periods.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosks.user_id')
        ->where('users.id', Auth::user()->id)
        ->where('status', 1)
        ->get();
        return view('periods.list')->with('periods', $periods);
    }

    public function listPeriods(){
        $periods = Period::select('periods.*')
        ->join('kiosks', 'kiosks.id', '=', 'periods.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosks.user_id')
        ->where('users.id', Auth::user()->id)
        ->where('status', 1)
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
        $periods = Period::where('kiosk_id', $kiosk_id)
        ->where('status', 1)
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
        $kiosks = Kiosk::where("user_id", Auth::user()->id)->where('status', 1)->get();
        return view('periods/form')
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
        $period = new Period;
        $period->time = $request->input('time');
        $period->value = $request->input('value');
        $period->kiosk_id = $request->input('kiosk_id');
        $period->save();
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
        $kiosks = Kiosk::where("user_id", Auth::user()->id)->where('status', 1)->get();
        $period = Period::find($id);
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
        $period = Period::find($id);
        $period->time = $request->input('time');
        $period->value = $request->input('value');
        $period->kiosk_id = $request->input('kiosk_id');
        $period->save();
        return redirect('period');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
