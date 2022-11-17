<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sales;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

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
        $period = now()->subMonths(7)->monthsUntil(now());
        $mont = [];
        foreach ($period as $date)
        {
            $mont[] = [
                'month' => $date->shortMonthName,
                'year' => $date->year,
                'profit' => $this->profitPerMonth($date->month, $date->year),
            ];
        }
        //var_dump($mont);
        //var_dump(json_decode($months[1]['month'])." ".json_decode($months[1]['year']));
        $sales = Sales::sum('total_price');
        $cash = Sales::sum('amnt_paid');
        $credit = $sales - $cash;
        $now = Carbon::now();
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');
        $cmons = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->get();
        $cmonc = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->get();
        $cmone = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereYear('expenses.created_at', Carbon::now()->year)
            ->whereMonth('expenses.created_at', Carbon::now()->month)
            ->get();
        $ctods = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereDate('sales.created_at', Carbon::today()->format('Y-m-d'))
            ->get();
        $ctodc = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereDate('sales.created_at', Carbon::today()->format('Y-m-d'))
            ->get();
        $ctode = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereDate('expenses.created_at', Carbon::today()->format('Y-m-d'))
            ->get();
        $profit_mon = $cmons[0]->total_sales - $cmonc[0]->total_cost - $cmone[0]->total_expenses;
        $profit_tod = $ctods[0]->total_sales - $ctodc[0]->total_cost - $ctode[0]->total_expenses;
        $mon = Sales::whereDate('created_at', $weekStartDate)->sum('total_price');
        $tue = Sales::whereDate('created_at', $now->startOfWeek()->addDays(1)->format('Y-m-d'))->sum('total_price');
        $wed = Sales::whereDate('created_at', $now->startOfWeek()->addDays(2)->format('Y-m-d'))->sum('total_price');
        $thu = Sales::whereDate('created_at', $now->startOfWeek()->addDays(3)->format('Y-m-d'))->sum('total_price');
        $fri = Sales::whereDate('created_at', $now->startOfWeek()->addDays(4)->format('Y-m-d'))->sum('total_price');
        $sat = Sales::whereDate('created_at', $now->startOfWeek()->addDays(5)->format('Y-m-d'))->sum('total_price');
        $sun = Sales::whereDate('created_at', $now->startOfWeek()->addDays(6)->format('Y-m-d'))->sum('total_price');
        return view('home', compact('cash', 'credit', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun','cmons','cmonc','cmone','profit_mon','ctods','ctodc','ctode','profit_tod','mont'));
    }

    function profitPerMonth($month, $year){
        $sales = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $month)
            ->get();
        $cost = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $month)
            ->get();
        $expense = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereYear('expenses.created_at', $year)
            ->whereMonth('expenses.created_at', $month)
            ->get();
        $profit = $sales[0]->total_sales - $cost[0]->total_cost - $expense[0]->total_expenses;

        return $profit;
    }

}
