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
            ->with('user', Auth::user())
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
        $kiosk->cnpj = $request->input('cnpj');
        $kiosk->extra_value = $request->input('extra-value');
        $kiosk->address = $request->input('address');
        $kiosk->address_number = $request->input('address_number');
        $kiosk->address_district = $request->input('address_district');
        $kiosk->address_city = $request->input('address_city');
        $kiosk->address_state = $request->input('address_state');
        $kiosk->postalcode = $request->input('postalcode');
        if($request->input('payment_code'))
            $kiosk->payment_code = $request->input('payment_code');
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
            ->with('user', Auth::user())
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
        $kiosk->cnpj = $request->input('cnpj');
        $kiosk->extra_value = $request->input('extra-value');
        $kiosk->address = $request->input('address');
        $kiosk->address_number = $request->input('address_number');
        $kiosk->address_district = $request->input('address_district');
        $kiosk->address_city = $request->input('address_city');
        $kiosk->address_state = $request->input('address_state');
        $kiosk->postalcode = $request->input('postalcode');
        $kiosk->save();
        return redirect('kiosk');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toogle($id)
    {
        $kiosk = Kiosk::find($id);
        if($kiosk->status)
            $kiosk->status = 0;
        else
            $kiosk->status = 1;
        $kiosk->save();
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
