<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Period;

class PeriodController extends Controller
{
    /**
    *Return the periods and values of rentals
    *@return \Illuminate\Http\Response
    */
    public function index(){
        $periods = Period::get();
        return response()->json($periods);
    }
}
