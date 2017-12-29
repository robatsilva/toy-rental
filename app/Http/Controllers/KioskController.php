<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Session;

use App\Http\Requests;

use App\Models\Kiosk;

class KioskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kiosks = Kiosk::where('user_id', Auth::user()->id)
            ->where('status', 1)
            ->orderBy("default", "desc")
            ->get();
        if($request->header('Content-Type') == 'JSON')
            return response()->json($kiosks);
        return view('kiosks.list')->with('kiosks', $kiosks);
    }

    public function listKiosk(Request $request)
    {
        $kiosks = Kiosk::where('user_id', Auth::user()->id)
            ->where('status', 1)
            ->orderBy("default", "desc")
            ->get();
        return response()->json($kiosks);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('kiosks/form')
            ->with('kiosk', null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $kioskDefault = Kiosk::where("user_id", Auth::user()->id)
                        ->where("default", 1)
                        ->first();
                        
        $kiosk = new Kiosk;
        $kiosk->user_id = Auth::user()->id;
        $kiosk->name = $request->input('name');
        $kiosk->tolerance = $request->input('tolerance');
        $kiosk->extra_value = $request->input('extra-value');
        $kiosk->default = 1;
        $kiosk->save();
        
        if($kioskDefault){
            $kioskDefault->default = 0;
            $kioskDefault->save();
        }
        return redirect('kiosk');
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
        $kiosk = Kiosk::find($id);
        return view('kiosks/form')
            ->with("kiosk", $kiosk);
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
        $kiosk = Kiosk::find($id);
        $kiosk->name = $request->input('name');
        $kiosk->tolerance = $request->input('tolerance');
        $kiosk->extra_value = $request->input('extra-value');
        $kiosk->save();
        return redirect('kiosk');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kiosk = Kiosk::find($id);
        $kiosk->delete();
        return redirect('kiosk');
    }

    public function setDefault($id)
    {
        $kioskDefault = Kiosk::where("user_id", Auth::user()->id)
                        ->where("default", 1)
                        ->first();
                        
        $kiosk = Kiosk::find($id);
        $kiosk->default = 1;
        $kiosk->save();
        
        if($kioskDefault){
            $kioskDefault->default = 0;
            $kioskDefault->save();
        }
        return redirect('kiosk');
    }
}
