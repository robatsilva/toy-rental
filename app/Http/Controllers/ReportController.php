<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;

use DB;
use Auth;
use App\Models\Rental;
use App\Models\Employe;
use App\Models\Cash;
use App\Models\CashFlow;

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
                    
                sum( TIMESTAMPDIFF(HOUR, init, if(END is not null, END, '" . Carbon::now() . "')) ) as total_time,
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

        $total = Rental::
            whereDate('init', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init'))))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->sum("value_cd");
        $total += Rental::
        whereDate('init', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init'))))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->sum("value_cc");
        $total += Rental::
        whereDate('init', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init'))))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->sum("value_di");
        
        $date = Carbon::createFromFormat('d/m/Y', ( $request->input('init')))->format('Y-m-d');

        $cashes = Cash::where("kiosk_id", $request->input("kiosk_id"))
        ->whereBetween(DB::raw('date(created_at)'), array($date, $date))
        ->where( function($query) use ($user) {
            if($user->kiosk_id){
                $query->where("employe_id", $user->id);
            }
        })
        ->with('employe')
        ->get();
        $cashFlows = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereBetween(DB::raw('date(created_at)'), array($date, $date))
        ->where( function($query) use ($user) {
            if($user->kiosk_id){
                $query->where("employe_id", $user->id);
            }
        })
        ->with('employe')
        ->get();
        
        $input = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init'))))
        ->sum('input');
        
        $output = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', ( $request->input('init'))))
        ->sum('output');

        $cash['rentals'] = $total;
        $cash['cashes'] = $cashes;
        $cash['cash_flows'] = $cashFlows;
        $cash['input'] = $input;
        $cash['output'] = $output;
        $cash['total'] = $input - $output + $total;
        return view('reports.cash-flow')
            ->with('cash', $cash)
            ->with('input', $request->input());
    }
    /**
     * get total of rentals group by payment way
     */
    public function reportByPaymentWay(Request $request)
    {
        $user = Employe::find(Auth::user()->id);

        $rentals = Rental::selectRaw("*, if(value_cc, 'Cartão de crédito', if(value_cd, 'Cartão de débito', if(value_di, 'Dinheiro', ''))) as payment_way,
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
            ->groupBy("value_cc")
            ->groupBy("value_cd")
            ->groupBy("value_di");
        
        $rentals = $rentals
                ->get();

        return view('reports.payment-way-table')
            ->with('rentals', $rentals)
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
        return view('reports.cash-flow')
            ->with('cash', null)
            ->with('input', null);
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
