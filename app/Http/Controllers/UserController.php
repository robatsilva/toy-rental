<?php

namespace App\Http\Controllers;
use Auth;
use Hash;
use Validator;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Kiosk;
use App\Models\User;

class UserController extends Controller
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        $user = Auth::user();
        return view('users.register')
            ->with("user", $user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();

        if (! Hash::check($input['password_old'],Auth::user()->password)){
            return redirect('user')->withErrors(['password_old' => 'Senha atual está incorreta'])->withInput();
        }

        $validator = Validator::make($request->all(), [
            'password'   => ["required"],
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            return redirect('user')
                ->withErrors($validator)
                ->withInput();
        }
        $user = Auth::user();

        $input['password'] = bcrypt($input['password']);//criptografa password

        $user->update($input);

        return redirect('sistema');
    }
}
