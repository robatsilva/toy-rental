<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Kiosk;
use App\User;
use App\Models\Employe;

class EmployeController extends Controller
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
    public function index()
    {
        $employes = Employe::selectRaw('users.*, users.status as status_employe')
        ->join('kiosk_user', 'kiosk_user.kiosk_id', '=', 'users.kiosk_id')
        ->where('user_id', Auth::user()->id)
        ->with('kiosk')
        ->get();
        return view('employes.list')->with('employes', $employes);
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
        return view('employes.register')
            ->with('employe', null)
            ->with('kiosks', $kiosks);
    }

    public function store(Request $request)
    {
        SecurityCheckController::securityCheck($request->input('kiosk_id'));
        if(!Employe::where('email', $request->input('email'))->get()->isEmpty())
            return redirect('employe/create')
                ->withErrors(['email' => 'Este e-mail já está cadastrado']);
        $employe = new Employe;
        $employe->name = $request->input('name');
        $employe->password = bcrypt($request->input('password'));
        $employe->email = $request->input('email');
        $employe->kiosk_id = $request->input('kiosk_id');
        $employe->save();
        return redirect('employe');
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
        $employe = Employe::find($id);
        SecurityCheckController::securityCheck($employe->kiosk_id);

        $kiosks = User::find(Auth::user()->id)
                ->kiosks()
                ->where('status', 1)->get();
        return view('employes.register')
            ->with('kiosks', $kiosks)
            ->with("employe", $employe);
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
        $employe = Employe::find($id);
        SecurityCheckController::securityCheck($employe->kiosk_id);
        SecurityCheckController::securityCheck($request->input('kiosk_id'));

        $employe->name = $request->input('name');
        if($request->input('password'))
            $employe->password = bcrypt($request->input('password'));
        $employe->email = $request->input('email');
        $employe->kiosk_id = $request->input('kiosk_id');
        $employe->save();
        return redirect('employe');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toogle($id)
    {
        $employe = Employe::find($id);
        SecurityCheckController::securityCheck($employe->kiosk_id);
        
        if($employe->status)
            $employe->status = 0;
        else
            $employe->status = 1;
        $employe->save();
        return redirect('employe');
    }
}
