<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use DB;
use Carbon\Carbon;

use App\Models\Toy;
use App\Models\Kiosk;

class ToyController extends Controller
{
    public function index()
    {
        $toys = Toy::select('toys.*')
        ->join('kiosks', 'kiosks.id', '=', 'toys.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosks.user_id')
        ->where('toys.status', 1)
        ->where('users.id', Auth::user()->id)
        ->get();
        return view('toys.list')->with('toys', $toys);
    }

    public function listToys(){
        $toys = Toy::join('kiosks', 'kiosks.id', '=', 'toys.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosks.user_id')
        ->where('status', 1)
        ->where('users.id', Auth::user()->id)
        ->get();
        return response()->json($toys);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getByKioskId($kiosk_id)
    {
        $toys = Toy::where('kiosk_id', $kiosk_id)
        ->where('status', 1)
        ->whereNotExists(function($query)
        {
            $query->select(DB::raw(1))
                  ->from('rentals')
                  ->whereRaw('toys.id = rentals.toy_id')
                  ->where(DB::raw("date(init)"), DB::raw("'". Carbon::now()->format("Y/m/d") . "'"))
                  ->whereRaw('status != "Encerrado" and status != "Cancelado"');
        })
        ->get();
        return response()->json($toys);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kiosks = Kiosk::where("user_id", Auth::user()->id)->where('status', 1)->get();
        return view('toys/form')
            ->with('kiosks', $kiosks)
            ->with('toy', null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $toy = new Toy;
        $toy->code = $request->input('code');
        $toy->description = $request->input('description');
        $toy->kiosk_id = $request->input('kiosk_id');

        // get current time and append the upload file extension to it,
        // then put that name to $photoName variable.
        $toy_img = time().'.'.$request->file("image")->getClientOriginalExtension();
        /*
        talk the select file and move it public directory and make avatars
        folder if doesn't exsit then give it that unique name.
        */
        $request->file("image")->move(public_path('images/toys-img'), $toy_img);
        $toy->image = $toy_img;
        $toy->save();
        return redirect('toy');
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
        $toy = Toy::find($id);
        return view('toys.form')
            ->with('kiosks', $kiosks)
            ->with("toy", $toy);
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
        $toy = Toy::find($id);
        $toy->code = $request->input('code');
        $toy->description = $request->input('description');
        $toy->kiosk_id = $request->input('kiosk_id');

        // get current time and append the upload file extension to it,
        // then put that name to $photoName variable.
        $toy_img = time().'.'.$request->file("image")->getClientOriginalExtension();
        /*
        talk the select file and move it public directory and make avatars
        folder if doesn't exsit then give it that unique name.
        */
        $request->file("image")->move(public_path('images/toys-img'), $toy_img);
        $toy->image = $toy_img;

        $toy->save();
        return redirect('toy');
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
