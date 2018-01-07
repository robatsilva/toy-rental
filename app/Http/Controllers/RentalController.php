<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;
use Auth;
use DB;

use App\Models\Rental;
use App\Models\Customer;
use App\Models\Toy;
use App\Models\Kiosk;
use App\Models\Period;
use App\Models\Employe;
use App\Models\Reason;

class RentalController extends Controller
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
     * Open home of rental screen
     */
    public function home()
    {
        $user = Employe::find(Auth::user()->id);
        $kiosks = Kiosk::where('user_id', $user->id)->where('status', 1)->get();
        if($kiosks->isEmpty())
            return redirect('cadastro');
        if($user->kiosk_id)
            $kiosk_id = $user->kiosk_id;
        else{
            $kiosk_id = Kiosk::
            where('user_id', $user->id)
            ->where('status', 1)
            ->where('default', "1")
            ->first()->id;
        }
        $reasons = Reason::where("kiosk_id", $kiosk_id)->get();
        $periods = Period::where("kiosk_id", $kiosk_id)
        ->where('status', 1)
        ->orderBy('time', "asc")
        ->get();

        return view('rentals.list')
            ->with('kiosk_id', $kiosk_id)
            ->with('reasons', $reasons)
            ->with('periods', json_encode($periods->toArray()));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $kiosk_id)
    {
        $kiosk = Kiosk::find($kiosk_id);
        $toys = Toy::
        where('toys.kiosk_id', $kiosk_id)
        ->where('toys.status', 1)
        ->with("kiosk")
        ->with(["rental" => function($query) use ($kiosk_id){
            $query->selectRaw("*, 
            (select time 
                from periods
                where periods.kiosk_id = " . $kiosk_id . "
                order by time asc 
            limit 1) as min_period,
            
            (select value 
                from periods
                where periods.kiosk_id = " . $kiosk_id . "
                and periods.id = period_id
                order by time asc 
            limit 1) as period_value_selected,

            (select time 
                from periods
                where periods.kiosk_id = " . $kiosk_id . "
                and periods.id = period_id
                order by time asc 
            limit 1) as period_selected,
            
            TIMESTAMPDIFF(MINUTE, init, if(END is not null, END, '" . Carbon::now() . "')) AS time_diff,
            
            ((select time_diff) - (if(extra_time > (select time_diff), 0, extra_time))) as time_considered,            
            (
                SELECT TIME
                FROM periods
                WHERE TIME <= if(time_considered < min_period, min_period, time_considered)
                and periods.kiosk_id = " . $kiosk_id . "
                ORDER BY TIME DESC
                LIMIT 1) AS period_calculated,
                
            
            if((select time_considered) <= ((select period_calculated) + tolerance), 0,
                ((select time_considered) - (select period_calculated))) AS time_exceded,

            (
            SELECT VALUE + ((select time_exceded) * extra_value)
            FROM periods
            WHERE TIME <= if(time_considered < min_period, min_period, time_considered)
            and periods.kiosk_id = " . $kiosk_id . "
            ORDER BY TIME DESC
            LIMIT 1)as period_value_calculated,

            if(((select period_value_selected) < (select period_value_calculated)) and ((select period_calculated) < (select period_selected)), 
                (select period_selected), 
                (select period_value_calculated)) as value_to_pay")
            ->where("kiosk_id", $kiosk_id)
            ->whereRaw('(status = "Pausado" or status = "Alugado")')
            ->with("period")
            ->with("customer");
        }])
        ->orderBy("toys.id")
        ->get();
        if($request->header('Content-Type') == 'JSON')
            return response()->json($toys);
        return view('rentals.rentals-toys')
            ->with('toys', $toys);
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
        if(!$request->input('customer.id'))
        {
            $cpf = $request->input('customer.cpf');
            $cpf = str_replace('.', '', $cpf);
            $cpf = str_replace('-', '', $cpf);
            $customer = new Customer;
            $customer->name = $request->input('customer.name');   
            $customer->cpf = $cpf;
            $customer->kiosk_id = $request->input('kiosk_id');
            $customer->save();
        }
        else{
            $customer = Customer::find($request->input('customer.id'));
        }
        $kiosk = Kiosk::find($request->input('kiosk_id'));

        $rental = Rental::where("customer_id", $customer->id)->where("status", "Alugado")->first();
        if($rental){
            $rental->toy_id = $request->input('toy_id');
        } else {
            $rental = new Rental;
            $rental->customer_id = $customer->id;
            $rental->kiosk_id = $request->input('kiosk_id');
            $rental->toy_id = $request->input('toy_id');
            $rental->period_id = $request->input('period.id');
            $rental->tolerance = $kiosk->tolerance;
            $rental->extra_time = 0;
            $rental->extra_value = $kiosk->extra_value;
            $rental->init = Carbon::now();
            $rental->status = "Alugado";
        }

        $rental->save();
        return;
    }

    /**
     * Change to next period of rental
     * @param int $id is the id of rental
     * @return call the index to update rental list
     */
    public function nextPeriod(Request $request, $id){
        $rental = Rental::find($id);
        $period = Period::where("id", ">", $rental->period_id)
                    ->where('status', 1)
                    ->where("kiosk_id", $rental->kiosk_id)
                    ->orderBy("id")->first();
        if($period)
            $rental->period_id = $period->id;
        else{
            $period = Period::where("kiosk_id", $rental->kiosk_id)
            ->where('status', 1)
            ->orderBy("id")->first();
            
            $rental->period_id = $period->id;
        }
        $rental->save();
        return;
    }
    /**
     * Change the status of rental to pause
     * @param int $id is the id of rental
     * @return call the index to update rental list
     */
    public function pause(Request $request, $id){
        $rental = Rental::find($id);
        $rental->status = "Pausado";
        $rental->end = Carbon::now();
        $rental->save();
        return;
    }

    /**
     * Change the status of rental to cancel
     * @param int $id is the id of rental
     * @return call the index to update rental list
     */
    public function cancel(Request $request, $id){
        $user = Employe::find(Auth::user()->id);
        $rental = Rental::find($id);
        $rental->reason_cancel = $request->input('reason_cancel');
        
        if(!$rental->reason_cancel){
            $reason = new Reason();
            $reason->text = $request->input('reason_cancel_other');
            $reason->kiosk_id = $rental->kiosk_id;
            $reason->save();
            $rental->reason_cancel = $request->input('reason_cancel_other');
        }
        $rental->status = "Cancelado";
        $rental->employe_id = $user->id;
        $rental->end = Carbon::now();
        $rental->save();
        return;
    }

    /**
     * Change the status of rental to finish and save the values of rental
     * @param int $id is the id of rental
     * @return call the index to update rental list
     */
    public function finish(Request $request){
        $user = Employe::find(Auth::user()->id);

        $rental = Rental::find($request->input('id'));
        
        $calc = $this->calculeRental($rental->id)->getData();
        if($request->input('payment_way')){
            if($request->input('payment_way') == "cc")
                $rental->value_cc = $calc->valueTotal;
                if($request->input('payment_way') == "cd")
                $rental->value_cd = $calc->valueTotal;
                if($request->input('payment_way') == "di")
                $rental->value_di = $calc->valueTotal;
            } else {
                $rental->value_cc = $request->input('value_cc');
                $rental->value_cd = $request->input('value_cd');
                $rental->value_di = $request->input('value_di');
        }
        $rental->period_id = $calc->period->id;
        $rental->status = "Encerrado";
        $rental->employe_id = $user->id;
        if(!$rental->end)
            $rental->end = Carbon::now();
        
        $rental->save();

        return;
    }

    public function extraTime(Request $request){
        $rental = Rental::find($request->input('id'));
        if($request->input('extra_time') == 0){
            $rental->extra_time = 0;
            $rental->save();
            return;
        }
        else
            $rental->extra_time += $request->input('extra_time');

        $rental->reason_extra_time = $request->input('reason_extra_time');
        
        if(!$rental->reason_extra_time){
            $reason = new Reason();
            $reason->text = $request->input('reason_extra_time_other');
            $reason->kiosk_id = $rental->kiosk_id;
            $reason->save();
            $rental->reason_extra_time = $request->input('reason_extra_time_other');
        }
        $rental->save();
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
        $rental = Rental::with("customer")->find($id);
        return response()->json($rental);
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

    public function calculeRental($id)
    {
        $rental = Rental::find($id);
        if($rental->end)
            $time_total = (new Carbon($rental->init))->diffInMinutes(new Carbon($rental->end));
        else
            $time_total = (new Carbon($rental->init))->diffInMinutes(Carbon::now());
        
        $time_considered = $time_total;
        if($time_total > $rental->extra_time)
            $time_considered = $time_total - $rental->extra_time; 
        
        $period = Period::where('time', '<=', $time_considered)
            ->where('kiosk_id', $rental->kiosk_id)
            ->orderBy('time', 'desc')
            ->first();
        if(!$period)
            $period = Period::
                        where('kiosk_id', $rental->kiosk_id)
                        ->orderBy('time', 'asc')->first();

        $timeExceeded = 0;
        $valueExceeded = 0;

        if($time_considered > ($rental->tolerance + $period->time)){
            $timeExceeded = $time_considered - $period->time;
            $valueExceeded = $timeExceeded * $rental->extra_value;
        }
        
        $valueTotal = $period->value + $valueExceeded;
        $data["rental"] = $rental;
        $data["timeTotal"] = $time_total;
        $data["timeConsidered"] = $time_considered;
        $data["valueExceeded"] = $valueExceeded;
        $data["timeExceeded"] = $timeExceeded;
        $data["valueTotal"] = $valueTotal;
        $data["period"] = $period;
        
        return response()->json($data);
    }
}
