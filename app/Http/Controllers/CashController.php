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
            $cash->value_open = str_replace(",",".", str_replace(".","",$request->input('value_open')));
        }
        $cash->value_close = str_replace(",",".", str_replace(".","",$request->input('value_close')));
        $cash->save();
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
