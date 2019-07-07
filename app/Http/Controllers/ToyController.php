<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Auth;
use DB;
use Carbon\Carbon;

use App\Models\Toy;
use App\Models\Rental;
use App\Models\Kiosk;
use App\Models\ToyLog;
use App\User;

class ToyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }
    
    public function index()
    {
        $toys = Toy::selectRaw('toys.*, toys.status as status_toy')
        ->join('kiosk_user', 'kiosk_user.kiosk_id', '=', 'toys.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosk_user.user_id')
        ->where('users.id', Auth::user()->id)
        ->get();
        return view('toys.list')->with('toys', $toys);
    }

    public function listToys(){
        $toys = Toy::join('kiosks', 'kiosks.id', '=', 'toys.kiosk_id')
        ->join('users', 'users.id', '=' ,'kiosks.user_id')
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
        SecurityCheckController::securityCheck($kiosk_id);
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
        $kiosks = User::find(Auth::user()->id)
                ->kiosks()
                ->where('status', 1)->get();
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
        SecurityCheckController::securityCheck($request->input('kiosk_id'));
        $toy = new Toy;
        $toy->code = $request->input('code');
        $toy->description = $request->input('description');
        $toy->kiosk_id = $request->input('kiosk_id');

        if($request->file("image")){
            // get current time and append the upload file extension to it,
            // then put that name to $photoName variable.
            $toy_img = time().'.'.$request->file("image")->getClientOriginalExtension();
            /*
            talk the select file and move it public directory and make avatars
            folder if doesn't exsit then give it that unique name.
            */
            $request->file("image")->move(public_path('images/toys-img'), $toy_img);
            $toy->image = $toy_img;
        }
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
        $toy = Toy::find($id);
        SecurityCheckController::securityCheck($toy->kiosk_id);

        $kiosks = User::find(Auth::user()->id)
                ->kiosks()
                ->where('status', 1)->get();
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
        SecurityCheckController::securityCheck($toy->kiosk_id);
        SecurityCheckController::securityCheck($request->input('kiosk_id'));
        $toy->code = $request->input('code');
        $toy->description = $request->input('description');
        $toy->kiosk_id = $request->input('kiosk_id');

        // get current time and append the upload file extension to it,
        // then put that name to $photoName variable.
        if($request->file("image")){
            $toy_img = time().'.'.$request->file("image")->getClientOriginalExtension();
            /*
            talk the select file and move it public directory and make avatars
            folder if doesn't exsit then give it that unique name.
            */
            $request->file("image")->move(public_path('images/toys-img'), $toy_img);
            $toy->image = $toy_img;
        }

        $toy->save();
        return redirect('toy');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toogle($id)
    {
        $toy = Toy::find($id);
        SecurityCheckController::securityCheck($toy->kiosk_id);
        
        if($toy->status)
            $toy->status = 0;
        else
            $toy->status = 1;
        $toy->save();
        return redirect('toy');
    }

    /**
     * check status of toy
     */
    public function check(Request $request, $id){
        $log = ToyLog::where('toy_id', $id)
        ->where('log_id', $request->input('id'))
        ->first();
        if(!$log){
            $log = new ToyLog();
            $log->log_id = $request->input('id');
            $log->toy_id = $id;
        }
        $log->qtd = $request->input('qtd');
        $log->tempo = $request->input('tempo');
        $log->save();

        $rental = Rental::where('toy_id', $id)
        ->where('status', 'Alugado')
        ->with('period')
        ->first();
        if($rental)
            return response()->json(["status" => "brinquedo ligar", "time" => Carbon::parse($rental->init)->addMinutes($rental->period->time)->format('H:i') ]);
        else
            return response()->json(["status"=>"brinquedo desligar"]);
    }
}
