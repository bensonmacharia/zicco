<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sales;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $period = now()->subMonths(7)->monthsUntil(now());
        $mont1 = [];
        $mont2 = [];
        foreach ($period as $date) {
            $mont1[] = [
                'month' => $date->shortMonthName,
                'year' => $date->year,
                'profit' => $this->profitPerMonthA($date->month, $date->year),
            ];
            $mont2[] = [
                'month' => $date->shortMonthName,
                'year' => $date->year,
                'profit' => $this->profitPerMonthB($date->month, $date->year),
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
        $cmons1 = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->where('shop_id', 1)
            ->get();
        $cmons2 = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->where('shop_id', 2)
            ->get();
        $cmonc1 = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->where('shop_id', 1)
            ->get();
        $cmonc2 = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->where('shop_id', 2)
            ->get();
        $cmone1 = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereYear('expenses.created_at', Carbon::now()->year)
            ->whereMonth('expenses.created_at', Carbon::now()->month)
            ->where('shop_id', 1)
            ->get();
        $cmone2 = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereYear('expenses.created_at', Carbon::now()->year)
            ->whereMonth('expenses.created_at', Carbon::now()->month)
            ->where('shop_id', 2)
            ->get();
        $ctods1 = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereDate('sales.created_at', Carbon::today()->format('Y-m-d'))
            ->where('shop_id', 1)
            ->get();
        $ctods2 = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereDate('sales.created_at', Carbon::today()->format('Y-m-d'))
            ->where('shop_id', 2)
            ->get();
        $ctodc1 = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereDate('sales.created_at', Carbon::today()->format('Y-m-d'))
            ->where('shop_id', 1)
            ->get();
        $ctodc2 = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereDate('sales.created_at', Carbon::today()->format('Y-m-d'))
            ->where('shop_id', 2)
            ->get();
        $ctode1 = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereDate('expenses.created_at', Carbon::today()->format('Y-m-d'))
            ->where('shop_id', 1)
            ->get();
        $ctode2 = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereDate('expenses.created_at', Carbon::today()->format('Y-m-d'))
            ->where('shop_id', 2)
            ->get();
        $profit_mon1 = $cmons1[0]->total_sales - $cmonc1[0]->total_cost - $cmone1[0]->total_expenses;
        $profit_mon2 = $cmons2[0]->total_sales - $cmonc2[0]->total_cost - $cmone2[0]->total_expenses;
        $profit_tod1 = $ctods1[0]->total_sales - $ctodc1[0]->total_cost - $ctode1[0]->total_expenses;
        $profit_tod2 = $ctods2[0]->total_sales - $ctodc2[0]->total_cost - $ctode2[0]->total_expenses;
        $mon1 = Sales::whereDate('created_at', $weekStartDate)->where('shop_id', 1)->sum('total_price');
        $mon2 = Sales::whereDate('created_at', $weekStartDate)->where('shop_id', 2)->sum('total_price');
        $tue1 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(1)->format('Y-m-d'))->where('shop_id', 1)->sum('total_price');
        $tue2 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(1)->format('Y-m-d'))->where('shop_id', 2)->sum('total_price');
        $wed1 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(2)->format('Y-m-d'))->where('shop_id', 1)->sum('total_price');
        $wed2 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(2)->format('Y-m-d'))->where('shop_id', 2)->sum('total_price');
        $thu1 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(3)->format('Y-m-d'))->where('shop_id', 1)->sum('total_price');
        $thu2 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(3)->format('Y-m-d'))->where('shop_id', 2)->sum('total_price');
        $fri1 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(4)->format('Y-m-d'))->where('shop_id', 1)->sum('total_price');
        $fri2 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(4)->format('Y-m-d'))->where('shop_id', 2)->sum('total_price');
        $sat1 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(5)->format('Y-m-d'))->where('shop_id', 1)->sum('total_price');
        $sat2 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(5)->format('Y-m-d'))->where('shop_id', 2)->sum('total_price');
        $sun1 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(6)->format('Y-m-d'))->where('shop_id', 1)->sum('total_price');
        $sun2 = Sales::whereDate('created_at', $now->startOfWeek()->addDays(6)->format('Y-m-d'))->where('shop_id', 2)->sum('total_price');
        return view('home', compact('cash', 'credit', 'mon1', 'mon2', 'tue1', 'tue2', 'wed1', 'wed2', 'thu1', 'thu2', 'fri1', 'sat1', 'sun1', 'fri2', 'sat2', 'sun2', 'cmons1', 'cmons2', 'cmonc1', 'cmonc2', 'cmone1', 'cmone2', 'profit_mon1', 'profit_mon2', 'ctods1', 'ctods2', 'ctodc1', 'ctodc2', 'ctode1', 'ctode2', 'profit_tod1', 'profit_tod2', 'mont1', 'mont2'));
    }

    function profitPerMonthA($month, $year)
    {
        $sales = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $month)
            ->where('shop_id', 1)
            ->get();
        $cost = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $month)
            ->where('shop_id', 1)
            ->get();
        $expense = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereYear('expenses.created_at', $year)
            ->whereMonth('expenses.created_at', $month)
            ->where('shop_id', 1)
            ->get();
        $profit = $sales[0]->total_sales - $cost[0]->total_cost - $expense[0]->total_expenses;

        return $profit;
    }

    function profitPerMonthB($month, $year)
    {
        $sales = Sales::selectRaw('SUM(sales.total_price) total_sales')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $month)
            ->where('shop_id', 2)
            ->get();
        $cost = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) total_cost')
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereYear('sales.created_at', $year)
            ->whereMonth('sales.created_at', $month)
            ->where('shop_id', 2)
            ->get();
        $expense = Expense::selectRaw('SUM(expenses.amount) total_expenses')
            ->whereYear('expenses.created_at', $year)
            ->whereMonth('expenses.created_at', $month)
            ->where('shop_id', 2)
            ->get();
        $profit = $sales[0]->total_sales - $cost[0]->total_cost - $expense[0]->total_expenses;

        return $profit;
    }
}
