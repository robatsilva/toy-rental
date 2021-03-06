<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Session;

use App\Http\Requests;

use App\Models\Kiosk;
use App\Models\User;

class KioskController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kiosks = User::find(Auth::user()->id)
            ->kiosks()
            ->orderBy("kiosk_user.default", "desc")
            ->get();
        if($request->header('Content-Type') == 'JSON')
            return response()->json($kiosks);
        return view('kiosks.list')->with('kiosks', $kiosks);
    }

    public function listKiosk(Request $request)
    {
        $kiosks = User::find(Auth::user()->id)
            ->kiosks()
            ->where('status', 1)
            ->orderBy("kiosk_user.default", "desc")
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
        $kioskDefault = User::find(Auth::user()->id)
                        ->kiosks()
                        ->where("kiosk_user.default", 1)
                        ->first();
                        
        $kiosk = new Kiosk;
        $kiosk->user_id = Auth::user()->id;
        $kiosk->name = $request->input('name');
        // $kiosk->tolerance = $request->input('tolerance');
        $kiosk->cnpj = $request->input('cnpj');
        // $kiosk->extra_value = $request->input('extra-value');
        $kiosk->credit_tax = str_replace(',', '.', $request->input('credit-tax'));
        $kiosk->debit_tax = str_replace(',', '.', $request->input('debit-tax'));
        $kiosk->royalty = str_replace(',', '.', $request->input('royalty'));
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
        SecurityCheckController::securityCheck($id);
        date_default_timezone_set($kiosk->timezone);

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
        SecurityCheckController::securityCheck($id);

        $kiosk = Kiosk::find($id);
        date_default_timezone_set($kiosk->timezone);

        $kiosk->name = $request->input('name');
        // $kiosk->tolerance = $request->input('tolerance');
        $kiosk->cnpj = $request->input('cnpj');
        // $kiosk->extra_value = $request->input('extra-value');
        $kiosk->credit_tax = str_replace(',', '.', $request->input('credit-tax'));
        $kiosk->debit_tax = str_replace(',', '.', $request->input('debit-tax'));
        $kiosk->royalty = str_replace(',', '.', $request->input('royalty'));
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
        SecurityCheckController::securityCheck($id);

        $kiosk = Kiosk::find($id);
        date_default_timezone_set($kiosk->timezone);

        if($kiosk->status)
            $kiosk->status = 0;
        else
            $kiosk->status = 1;
        $kiosk->save();
        return redirect('kiosk');
    }

    public function setDefault($id)
    {
        SecurityCheckController::securityCheck($id);

        $kioskDefault = User::find(Auth::user()->id)
                        ->kiosks()
                        ->where("kiosk_user.default", 1)
                        ->first();
                        
                        
        User::find(Auth::user()->id)
            ->kiosks()
            ->updateExistingPivot($id, ['default'=> 1]);
        
        if($kioskDefault){
            User::find(Auth::user()->id)
                    ->kiosks()
                    ->where("kiosk_user.default", 1)
                    ->updateExistingPivot($kioskDefault->id, ['default'=> 0]);
        }
        return redirect('kiosk');
    }
}
