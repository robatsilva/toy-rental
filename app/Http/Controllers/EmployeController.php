<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Kiosk;
use App\Models\User;
use App\Models\Employe;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employes = Employe::select('users.*')
        ->join('kiosks', 'kiosks.id', '=', 'users.kiosk_id')
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
        $kiosks = Kiosk::where("user_id", Auth::user()->id)->get();
        return view('employes.register')
            ->with('kiosks', $kiosks);
    }

    public function store(Request $request)
    {
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
        $kiosks = Kiosk::where("user_id", Auth::user()->id)->get();
        $employe = Employe::find($id);
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
    public function destroy($id)
    {
        //
    }
}
