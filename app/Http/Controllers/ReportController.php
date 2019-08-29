<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;

use DB;
use Auth;

use App\Models\Rental;
use App\Models\Employe;
use App\Models\User;
use App\Models\Cash;
use App\Models\Kiosk;
use App\Models\CashFlow;
use App\Models\CashDrawer;

class ReportController extends Controller
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
            SecurityCheckController::securityCheck($request->input('kiosk_id'));
            
            $kiosk = Kiosk::find($request->input("kiosk_id"));
            date_default_timezone_set($kiosk->timezone);
            // dd($kiosk->timezone);
        }
    }

    /**
     * $request has -> input() that have init and end date for report
     * $request has -> input() that have group by flag to group report on toy
     */
    public function reportByDate(Request $request)
    {
        $user = Employe::find(Auth::user()->id);
        if($user->type == '3'){
            $user->kiosk_id = 0;
            // $request->merge(['init' => date('d/m/Y')]);
            // $request->merge(['end' => date('d/m/Y')]);
        }

        $total = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_cd");
        $total += Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_id", $user->id);
                }
            })
            ->sum("value_cc");
        $total += Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
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
     * get total of rentals group by employe
     */
    public function reportByEmployes(Request $request)
    {
        $user = Employe::find(Auth::user()->id);

        $rentals = Rental::selectRaw("*, count(id) as qtd,
                sum(value_cc) + sum(value_cd) + sum(value_di) as total_pay
                    ")
            ->where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id",$request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->where( function($query) use ($user) {
                if($user->kiosk_id){
                    $query->where("employe_init_id", $user->id);
                }
            })
            ->orderBy("init", "desc")
            ->with("employe");
        
        $rentalsTotal = $rentals
            ->count();

        $rentals = $rentals
            ->groupBy("employe_init_id")
            ->get();


        return view('reports.employe-table')
            ->with('rentals', $rentals)
            ->with('rentals_total', $rentalsTotal)
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
        $cash['total'] = number_format((float)($input - $output + $total), 2, '.', '');
        $cash['input_day'] = $inputDay;
        $cash['output_day'] = $outputDay;
        $cash['total_day'] = number_format((float)($inputDay - $outputDay + $totalDay), 2, '.', '');
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
        $kiosk = Kiosk::find($request->input("kiosk_id"));
        $cashDrawers = CashDrawer::where('kiosk_id', $request->input("kiosk_id"))
                        ->where('status', 1)
                        ->get();
                        
        $total_cc = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->sum("value_cc");
        
        $total_cd = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->sum("value_cd");

        $total_di = Rental::
            where(DB::raw('date(init)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
            ->where("kiosk_id", $request->input("kiosk_id"))
            ->where("status", "!=", "Cancelado")
            ->sum("value_di");
        
        
        $rentals = Rental::selectRaw("*, 
            date(init) as data_inicio, 
            if(value_cc, 'Cartão de crédito', if(value_cd, 'Cartão de débito', if(value_di, 'Dinheiro', ''))) as payment_way,
            if(value_cc, 'CC', if(value_cd, 'CD', if(value_di, 'DI', ''))) as payment_type,
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
        ->selectRaw("(sum(value_cc) - sum(value_cc) * " . $kiosk->credit_tax / 100 . ") + (sum(value_cd) - sum(value_cd) * " . $kiosk->debit_tax / 100 . ") + sum(value_di) as total_pay_sem_taxa")
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
            
        $cash_query = CashFlow::where("kiosk_id", $request->input("kiosk_id"))
        ->where(DB::raw('date(created_at)'), 'between', DB::raw("'" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('init')))) . "' and '" . date('Y-m-d', strtotime(str_replace('/', '-', $request->input('end')))) . "'"))
        ->where( function($query) use ($user, $request) {
            if($request->input('check_employe')){
                $query->where("employe_id", $user->id);
            }
        });
        
        $cash_input = $cash_query->sum('input');
        $cash_output = $cash_query->sum('output');
        
        $cash_query->groupBy(DB::raw("date(created_at)"));
        
        $cash_input_days = clone($cash_query);
        $cash_input_days = $cash_input_days->where('input', '>', '0')
        ->select(DB::raw("*, sum(input) as input"))
        ->get();
        
        $cash_output_days = clone($cash_query);
        $cash_output_days = $cash_output_days->where('output', '>', '0')
        ->select(DB::raw("*, sum(output) as output"))
        ->get();
        
        $days->each(function ($day) use ($cash_input_days, $cash_output_days) {
            $input_day = $cash_input_days->filter(function($item) use ($day) {
                return $item->created_at->format('Y-m-d') == Carbon::createFromFormat('Y-m-d H:i:s', $day->init)->format('Y-m-d');
            })->first();
            $day->setAttribute('total_liquido', $day->total_pay);
            $day->setAttribute('total_liquido_sem_taxa', $day->total_pay_sem_taxa);
            if($input_day){
                $day->setAttribute('input', $input_day->input);
                $day->setAttribute('total_liquido', $input_day->input + $day->total_liquido);
                $day->setAttribute('total_liquido_sem_taxa', $input_day->input + $day->total_liquido_sem_taxa);
            }
            
            $output_day = $cash_output_days->filter(function($item) use ($day) {
                return $item->created_at->format('Y-m-d') == Carbon::createFromFormat('Y-m-d H:i:s', $day->init)->format('Y-m-d');
            })->first();
            if($output_day){
                $day->setAttribute('output', $output_day->output);
                $day->setAttribute('total_liquido', $day->total_liquido - $output_day->output);
                $day->setAttribute('total_liquido_sem_taxa', $day->total_liquido_sem_taxa - $output_day->output);
            }
        });
        $total_cc_liquid = $total_cc - ($total_cc * $kiosk->credit_tax / 100);
        $total_cd_liquid = $total_cd - ($total_cd * $kiosk->debit_tax / 100);
        $total_period = $total_cc + $total_cd + $total_di;
        $total_period_sem_taxa = $total_cc_liquid + $total_cd_liquid + $total_di;
        
        return view('reports.payment-way-table')
        ->with('rentals', $rentals)
        ->with('kiosk', $kiosk)
        ->with('total_cc', $total_cc)
        ->with('total_cd', $total_cd)
        ->with('total_di', $total_di)
        ->with('total_cc_liquid', $total_cc_liquid)
        ->with('total_cd_liquid', $total_cd_liquid)
        ->with('total_period', $total_period)
        ->with('total_period_sem_taxa', $total_period_sem_taxa)
        ->with('cash_input', $cash_input)
        ->with('cash_output', $cash_output)
        ->with('total_liquido', $total_period + $cash_input - $cash_output)
        ->with('total_liquido_sem_taxa', $total_period_sem_taxa + $cash_input - $cash_output)
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
    
    public function employes()
    {
        return view('reports.employe-table')
            ->with('rentals', null)
            ->with('rentals_total', null)
            ->with('input', null);
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
