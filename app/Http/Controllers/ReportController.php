<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;

use DB;
use Auth;
use App\Models\Rental;
use App\Models\Employe;
use App\User;
use App\Models\Cash;
use App\Models\CashFlow;
use App\Models\CashDrawer;

class ReportController extends Controller
{
    /**
     * $request has -> input() that have init and end date for report
     * $request has -> input() that have group by flag to group report on toy
     */
    public function reportByDate(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        $total = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_cd");
        $total += Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_cc");
        $total += Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_di");
        $rentals = Rental::selectRaw("*, rentals.status as rental_status, (value_cd + value_cc + value_di) as total_pay,
                
                TIMESTAMPDIFF(MINUTE, init, if(END is not null, END, '" . Carbon::now() . "')) AS time_diff,
                
                ((select time_diff) - (if(extra_time > (select time_diff), 0, extra_time))) as time_considered,                            
                if((select time_considered) <= (time + tolerance), 0,
                    ((select time_considered) - time)) AS time_exceded")
            ->join('periods', 'periods.id', '=', 'period_id')
            ->where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("rentals.kiosk_id", $request->input("kiosk_id"))
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->orderBy("init", "desc")
            ->with('employe')
            ->get();

        return view('reports.rental-table')
            ->with('rentals', $rentals)
            ->with('input', $request->input())
            ->with('resume', $total);
    }

    /**
     * get total of rentals group by toys
     */
    public function reportByToys(Request $request)
    {
        $user = Employe::find(Auth::user()->id);

        $rentals = Rental::selectRaw("*, 
                    
                sum( TIMESTAMPDIFF(MINUTE, init, if(end is not null, end, '" . Carbon::now() . "')) ) / 60 as total_time,
                sum(value_cc) + sum(value_cd) + sum(value_di) as total_pay
                    ")
            ->where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id",$request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->orderBy("init", "desc")
            ->groupBy("toy_id")
            ->with("toy");
        
        $rentals = $rentals
                ->get();

        return view('reports.toys-table')
            ->with('rentals', $rentals)
            ->with('input', $request->input());
    }
    /**
     * get cash and cash-flow registers
     */
    public function reportByCash(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        
        $cashDrawers = CashDrawer::where('kiosk_id', $request->input("kiosk_id"))
                        ->where('status', 1)
                        ->get();

        $cashOpen = Cash::where('employe_id', $user->id)
            ->whereRaw("updated_at = created_at")
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->first();
        
        $cashOpenOld = Cash::where('employe_id', $user->id)
            ->where("created_at", "<", date("Y-m-d"))
            ->whereRaw("updated_at = created_at")
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->first();

        $cashDrawerId = $cashDrawers[0]->id;

        if($cashOpen){
            $cashDrawerId = $cashOpen->cash_drawer_id;
        }

        if($request->input("cash_drawer")){
            $cashDrawerId = $request->input("cash_drawer");   
        }

        $total_cc = Rental::
        whereDate('init', '=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where( function($query) use ($user, $request) {
                if($request->input('check_employe')){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_cc");
        
        $total_cd = Rental::
        whereDate('init', '=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where( function($query) use ($user, $request) {
                if($request->input('check_employe')){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_cd");
        
        $total_cartao = $total_cc + $total_cd;

        $total = Rental::
        whereDate('init', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("cash_drawer_id", $cashDrawerId)
            ->where( function($query) use ($user, $request) {
                if($request->input('check_employe')){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_di");
        
        $totalDay = Rental::
        whereDate('init', '=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("cash_drawer_id", $cashDrawerId)
            ->where( function($query) use ($user, $request) {
                if($request->input('check_employe')){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_di");
        
        $date = Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d');

        $cashes = Cash::where("kiosk_id", $request->input("kiosk_id"))
        ->whereBetween(DB::raw('date(created_at)'), array($date, $date))
        ->where("cash_drawer_id", $cashDrawerId)
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        })
        ->with('employe')
        ->get();

        $cashFlows = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereBetween(DB::raw('date(created_at)'), array($date, $date))
        ->where("cash_drawer_id", $cashDrawerId)
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        })
        ->with('employe')
        ->get();

        $input = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
        ->where("cash_drawer_id", $cashDrawerId)
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        })
        ->sum('input');
        
        $output = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
        ->where("cash_drawer_id", $cashDrawerId)
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        })
        ->sum('output');
        
        $inputDay = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereDate('created_at', '=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
        ->where("cash_drawer_id", $cashDrawerId)
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        })
        ->sum('input');
        
        $outputDay = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereDate('created_at', '=', Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d'))
        ->where("cash_drawer_id", $cashDrawerId)
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        })
        ->sum('output');

        $isCashOpen = Cash::where('employe_id', $user->id)->whereRaw('updated_at = created_at')->get();

        $haveCashOpen = null;

        if($request->input("close_cash")){
            $haveCashOpen = $cashOpen;
        }

        $cash_drawers['cash_drawers'] = $cashDrawers;
        $cash_drawers['cash_drawer_id'] = $cashDrawerId;
        $cash['rentals'] = $total;
        $cash['rentals_day'] = $totalDay;
        $cash['cashes'] = $cashes;
        $cash['cashes_old'] = $cashOpenOld;
        $cash['cash_flows'] = $cashFlows;
        $cash['input'] = $input;
        $cash['output'] = $output;
        $cash['total'] = $input - $output + $total;
        $cash['input_day'] = $inputDay;
        $cash['output_day'] = $outputDay;
        $cash['total_day'] = $inputDay - $outputDay + $totalDay;
        $cash['total_cartao'] = $total_cartao;
        return view('reports.cash-flow')
            ->with('cash', $cash)
            ->with('cash_drawers', $cash_drawers)
            ->with('input', $request->input())
            ->with('show_cash', $isCashOpen->isEmpty() && $user->kiosk_id)
            ->with('close_cash', $haveCashOpen);
    }
    /**
     * get total of rentals group by payment way
     */
    public function reportByPaymentWay(Request $request)
    {
        $user = Employe::find(Auth::user()->id);


        $total_cc = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->sum("value_cc");
        
        $total_cd = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->sum("value_cd");

        $total_di = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->sum("value_di");
        
        $total_period = $total_cc + $total_cd + $total_di;
        
        $rentals = Rental::selectRaw("*, date(init) as data_inicio, if(value_cc, 'Cartão de crédito', if(value_cd, 'Cartão de débito', if(value_di, 'Dinheiro', ''))) as payment_way,
                sum(value_cc) + sum(value_cd) + sum(value_di) as total_pay
                    ")
            ->where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id",$request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->orderBy("init", "desc")
            ->groupBy("payment_way", DB::raw("date(init)"));
        
        $rentals = $rentals
                ->get();

        $days = Rental::selectRaw("*, date(init) as data_inicio, sum(value_cc) + sum(value_cd) + sum(value_di) as total_pay")
            ->where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id",$request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->orderBy("init", "desc")
            ->groupBy(DB::raw("date(init)"));
        
        $days = $days
                ->get();

        return view('reports.payment-way-table')
            ->with('rentals', $rentals)
            ->with('total_cc', $total_cc)
            ->with('total_cd', $total_cd)
            ->with('total_di', $total_di)
            ->with('total_period', $total_period)
            ->with('days', $days)
            ->with('input', $request->input());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rentals()
    {
        return view('reports.rental-table')
            ->with('rentals', null)
            ->with('input', null)
            ->with('resume', null);
    }

    public function toys()
    {
        return view('reports.toys-table')
            ->with('rentals', null)
            ->with('input', null);
    }
    
    public function cash()
    {
        $user = Employe::find(Auth::user()->id);

        $kiosks = User::find(Auth::user()->id)
            ->kiosks()
            ->get();
            

        if($user->kiosk_id)
            $kiosk_id = $user->kiosk_id;
        else{
            $kiosk_id = User::find(Auth::user()->id)
            ->kiosks()
            ->where('kiosk_user.user_id', $user->id)
            ->where('status', 1)
            ->where('kiosk_user.default', "1")
            ->first()->id;
        }

        $cashDrawers = CashDrawer::where('kiosk_id', $kiosk_id)
                        ->where('status', 1)
                        ->get();

        $cashOpen = Cash::where('employe_id', $user->id)
            ->where("kiosk_id", $kiosk_id)
            ->whereRaw('updated_at = created_at')
            ->first();

        if($cashOpen){
            $cash_drawers['cash_drawers'] = $cashDrawers;
            $cash_drawers['cash_drawer_id'] = $cashOpen->cash_drawer_id;
        } else {
            $cash_drawers = null;
        }
            
        $cash = null;
        
        return view('reports.cash-flow')
            ->with('cash', $cash)
            ->with('cash_drawers', $cash_drawers)
            ->with('input', null)
            ->with('showCash', false);
    }
    public function cashClose()
    {
        $user = Employe::find(Auth::user()->id);
        if(!$user->kiosk_id){
            return redirect('/logout');
        }
        $cash = Cash::where('employe_id', $user->id)->whereRaw('updated_at = created_at')->get();
        if(!$cash->isEmpty()){
            return view('reports.cash-flow')
                ->with('cash', null)
                ->with('cash_drawers', null)
                ->with('input', null)
                ->with('showCash', false)
                ->with('closeCash', true);
        }
        
        return redirect('/logout');
    }
    
    public function paymentWay()
    {
        return view('reports.payment-way-table')
            ->with('rentals', null)
            ->with('input', null);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function toogle($id)
    {
        //
    }
}
