<?php

namespace App\Http\Controllers;

use App\Models\Sales;
use App\Models\Stock;
use Carbon\Carbon;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $sales = Sales::sum('total_price');
        $cash = Sales::sum('amnt_paid');
        $credit = $sales - $cash;
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        $mon = Sales::whereDate('updated_at', $weekStartDate)->sum('total_price');
        $tue = Sales::whereDate('updated_at', $now->startOfWeek()->addDays(1)->format('Y-m-d'))->sum('total_price');
        $wed = Sales::whereDate('updated_at', $now->startOfWeek()->addDays(2)->format('Y-m-d'))->sum('total_price');
        $thu = Sales::whereDate('updated_at', $now->startOfWeek()->addDays(3)->format('Y-m-d'))->sum('total_price');
        $fri = Sales::whereDate('updated_at', $now->startOfWeek()->addDays(4)->format('Y-m-d'))->sum('total_price');
        $sat = Sales::whereDate('updated_at', $now->startOfWeek()->addDays(5)->format('Y-m-d'))->sum('total_price');
        $sun = Sales::whereDate('updated_at', $now->startOfWeek()->addDays(6)->format('Y-m-d'))->sum('total_price');
        return view('home', compact('cash', 'credit', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'));
    }

}
