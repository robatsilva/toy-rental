<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;
use Auth;
use DB;

use App\Models\Rental;
use App\Models\Customers;
use App\Models\Toy;
use App\Models\Kiosk;
use App\Models\Period;
use App\Models\Employe;
use App\User;
use App\Models\Reason;
use App\Models\Cash;

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
        $kiosks = User::find(Auth::user()->id)
            ->kiosks()
            ->get();
        // $kiosks = Kiosk::where('user_id', $user->id)->where('status', 1)->get();
        
        if($kiosks->isEmpty() && !$user->kiosk_id)
            return redirect('cadastro');

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

        $kiosk = Kiosk::find($kiosk_id);
        
        $cash = Cash::where('employe_id', $user->id)
        ->whereRaw('updated_at = created_at')->get();
        
        if($cash->isEmpty() && $user->kiosk_id)
            return redirect('report/cash');

        $reasons = Reason::where("kiosk_id", $kiosk_id)->get();
        $periods = Period::where("kiosk_id", $kiosk_id)
        ->where('status', 1)
        ->orderBy('time', "asc")
        ->get();

        return view('rentals.list')
            ->with('kiosk', $kiosk)
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
            
                    TIMESTAMPDIFF(MINUTE, init, if(END is not null, END, '" . Carbon::now() . "')) AS time_diff")
            ->where("kiosk_id", $kiosk_id)
            ->whereRaw('(status = "Pausado" or status = "Alugado")')
            ->with("period")
            ->with("customer");
        }])
        ->orderBy("toys.id")
        ->get();

        foreach($toys as $toy){
            if($toy->rental){
                $calc = $this->calculeRental($toy->rental->id)->getData();
                $toy->rental['value_to_pay'] = $calc->valueTotal;
            }
        }

        if($request->header('Content-Type') == 'JSON')
            return response()->json($toys);
        return view('rentals.rentals-toys')
            ->with('kiosk', $kiosk)
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
        $user = Employe::find(Auth::user()->id);

        if(!$request->input('customer.id'))
        {
            $cpf = $request->input('customer.cpf');
            $cpf = str_replace('.', '', $cpf);
            $cpf = str_replace('-', '', $cpf);
            $customer = new Customers;
            $customer->name = $request->input('customer.name');   
            $customer->cpf = $cpf;
            $customer->kiosk_id = $request->input('kiosk_id');
            $customer->save();
        }
        else{
            $customer = Customers::find($request->input('customer.id'));
        }
        $kiosk = Kiosk::find($request->input('kiosk_id'));

        if($request->input('customer.change_toy')=='true'){
 			$rental = Rental::find($request->input('customer.rental_toy'));
        } else {
        	$rental = Rental::where("customer_id", $customer->id)->where("status", "Alugado")->first();
        }
        
        if($rental && $request->input('customer.change_toy') == 'true'){
            $rental->toy_id = $request->input('toy_id');
            $rental->save();
        } else {
            $rental = new Rental;
            $rental->customer_id = $customer->id;
            $rental->kiosk_id = $request->input('kiosk_id');
            $rental->toy_id = $request->input('toy_id');
            $rental->period_id = $request->input('period.id');
            $rental->tolerance = $kiosk->tolerance;
            $rental->extra_time = 0;
            $rental->extra_value = $kiosk->extra_value;
            $rental->employe_id = $user->id;
            $rental->init = Carbon::now();
            $rental->status = "Alugado";
            $rental->save();
        }

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
     * Change the status of rental to rental
     * @param int $id is the id of rental
     * @return call the index to update rental list
     */
    public function start(Request $request, $id){
        $rental = Rental::find($id);
        $rental->status = "Alugado";
        
        $time_paused = (new Carbon($rental->end))->diffInMinutes(Carbon::now());
        $rental->extra_time += $time_paused;
        
        $rental->reason_extra_time .= " / Pausado pelo sistema";

        $rental->end = null;
        $rental->save();
        return;
    }
    
    public function back($id){
        $rental = Rental::where('toy_id', $id)
        ->where('end', '>', Carbon::now()->subMinutes(5)->toDateTimeString())
        ->orderBy("end", "desc")
        ->first();
        $rental->status = "Pausado";
        $rental->value_cc = 0;
        $rental->value_cd = 0;
        $rental->value_di = 0;
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
        if(!$rental->end)
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

        
        
        if(!$request->input('reason_extra_time')){
            $reason = new Reason();
            $reason->text = $request->input('reason_extra_time_other');
            $reason->kiosk_id = $rental->kiosk_id;
            $reason->save();
            $rental->reason_extra_time .= " / " . $request->input('reason_extra_time_other');
        } else {
            $rental->reason_extra_time .= " / " . $request->input('reason_extra_time');
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

        // if(!$period)
        //     $period = Period::
        //                 where('kiosk_id', $rental->kiosk_id)
        //                 ->orderBy('time', 'asc')->first();

        $next_period = Period::where('time', '>=', $time_considered)
            ->where('kiosk_id', $rental->kiosk_id)
            ->orderBy('time', 'asc')
            ->first();

        $timeExceeded = 0;
        $valueExceeded = 0;

        if($period){
            if($time_considered > ($rental->tolerance + $period->time)){
                $timeExceeded = $time_considered - $period->time;
                $valueExceeded = $timeExceeded * $rental->extra_value;
            }
            
            $valueTotal = $period->value + $valueExceeded;
        } else {
            $valueTotal = $time_considered * $rental->extra_value;
        }

        if($next_period && ($valueTotal > $next_period->value))
            $valueTotal = $next_period->value;

        $data["rental"] = $rental;
        $data["timeTotal"] = $time_total;
        $data["timeConsidered"] = $time_considered;
        $data["valueExceeded"] = $valueExceeded;
        $data["timeExceeded"] = $timeExceeded;
        $data["valueTotal"] = $valueTotal;
        $data["period"] = $period ? $period : $next_period;
        
        return response()->json($data);
    }

    public function send($id){
        $rentals = Rental::where("kiosk_id", $id)
        ->get();

        $ch = curl_init();
        

        curl_setopt($ch, CURLOPT_URL,"http://localhost:8000/rental/receive");
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $rentals->toArray());
        
            // // // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $server_output = curl_exec($ch);
        
        curl_close ($ch);
        
        // // Further processing ...
        return $server_output;
        

        
    }

    public function receive(Request $request){
        return $request->input();
    }
}
    