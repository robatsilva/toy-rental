<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;


use App\Http\Requests;
use App\Models\Cash;
use App\Models\Kiosk;
use App\Models\CashFlow;
use App\Models\Employe;


class CashController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        if($request->input("kiosk_id"))
        {
            SecurityCheckController::securityCheck($request->input("kiosk_id"));
            $kiosk = Kiosk::find($request->input("kiosk_id"));
            date_default_timezone_set($kiosk->timezone);
            // dd($kiosk->timezone);
        }
    }

    public function registerCashFlow(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        $cash = new CashFlow;
        $cash->kiosk_id = $request->input('kiosk_id');
        $cash->employe_id = $user->id;
        $cash->cash_drawer_id = $request->input('cash_drawer');
        $cash->created_at = Carbon::createFromFormat('d/m/Y', $request->input('created_at'));
        $cash->input = str_replace(",",".", str_replace(".","",$request->input('input')));
        $cash->output = str_replace(",",".", str_replace(".","",$request->input('output')));
        $cash->description = $request->input('description');
        if($request->file("file")){

            $cashFile = time().'.'.$request->file("file")->getClientOriginalExtension();
            /*
            talk the select file and move it public directory and make avatars
            folder if doesn't exsit then give it that unique name.
            */
            $request->file("file")->move(public_path('files'), $cashFile);
            $cash->file = $cashFile;
        }
        $cash->save();
        return view('reports.cash-flow')
        ->with('cash', null)
        ->with('cash_drawers', null)
        ->with('input', null)
        ->with('cash_save', true);
    }
    
    public function deleteCashFlow($id){
        if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first()){
            $cashFlow = CashFlow::find($id);
            SecurityCheckController::securityCheck($cashFlow->kiosk_id);
            $cashFlow->delete();
        }
    }
    public function registerCash(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        if($request->input('id')){
            $cash = Cash::find($request->input('id'));
            SecurityCheckController::securityCheck($cash->kiosk_id);
        }
        else{
            $cash = new Cash;
            $cash->kiosk_id = $request->input('kiosk_id');
            $cash->employe_id = $user->id;
            $cash->created_at = Carbon::createFromFormat('d/m/Y', $request->input('created_at'));
            $cash->value_open = $request->input('value_open');
            $cash->cash_drawer_id = $request->input('cash_drawer');
            $cash->a005 = $request->input('a005');
            $cash->a010 = $request->input('a010');
            $cash->a025 = $request->input('a025');
            $cash->a050 = $request->input('a050');
            $cash->a1 = $request->input('a1');
            $cash->a2 = $request->input('a2');
            $cash->a5 = $request->input('a5');
            $cash->a10 = $request->input('a10');
            $cash->a20 = $request->input('a20');
            $cash->a50 = $request->input('a50');
            $cash->a100 = $request->input('a100');
            $cash->a200 = $request->input('a200');
        }
        $cash->value_close = $request->input('value_close');
        $cash->f005= $request->input('f005');
        $cash->f010 = $request->input('f010');
        $cash->f025 = $request->input('f025');
        $cash->f050 = $request->input('f050');
        $cash->f1 = $request->input('f1');
        $cash->f2 = $request->input('f2');
        $cash->f5 = $request->input('f5');
        $cash->f10 = $request->input('f10');
        $cash->f20 = $request->input('f20');
        $cash->f50 = $request->input('f50');
        $cash->f100 = $request->input('f100');
        $cash->f200 = $request->input('f200');
        
        $cash->save();

        if($request->input('close_cash'))
            return redirect('/logout');

        // ->with('input', ['init' => $request->input('created_at')])
        return view('reports.cash-flow')
        ->with('cash', null)
        ->with('cash_drawers', null)
        ->with('input', $request->input())
        ->with('cash_save', true);
    }

    public function deleteCash($id){
        if (!Auth::guest() && Auth::user()->permissions()->get()->where('name', 'franqueador')->first()){
            $cash = Cash::find($id);
            SecurityCheckController::securityCheck($cash->kiosk_id);
            $cash->delete();
        }
    }

}
