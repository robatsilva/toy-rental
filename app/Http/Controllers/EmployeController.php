<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Kiosk;
use App\Models\User;

class EmployeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

   /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function store(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'kiosk_id' => $data['kiosk_id'],
            'password' => bcrypt($data['password']),
        ]);
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
        //
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
        //
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
