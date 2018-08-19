<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;


use App\Http\Requests;
use App\Models\Cash;
use App\Models\CashFlow;
use App\Models\Employe;


class CashController extends Controller
{
    public function registerCashFlow(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        $cash = new CashFlow;
        $cash->kiosk_id = $request->input('kiosk_id');
        $cash->employe_id = $user->id;
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
        ->with('input', null)
        ->with('cash_save', true);
    }
    
    public function deleteCashFlow($id){
        $cashFlow = CashFlow::find($id);
        $cashFlow->delete();
    }
    public function registerCash(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        if($request->input('id')){
            $cash = Cash::find($request->input('id'));
        }
        else{
            $cash = new Cash;
            $cash->kiosk_id = $request->input('kiosk_id');
            $cash->employe_id = $user->id;
            $cash->created_at = Carbon::createFromFormat('d/m/Y', $request->input('created_at'));
            $cash->value_open = $request->input('value_open');
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
        
        $cash->save();

        if($request->input('close_cash'))
            return redirect('/logout');

        return view('reports.cash-flow')
        ->with('cash', null)
        ->with('input', ['init' => $request->input('created_at')])
        ->with('cash_save', true);
    }

    public function deleteCash($id){
        $cash = Cash::find($id);
        $cash->delete();
    }

}
