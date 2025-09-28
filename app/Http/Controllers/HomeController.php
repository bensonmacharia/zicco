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
        // Get monthly profits for each shop for the last 8 months
        $month_profits = $this->getShopProfitPerMonth();
        $montpA = $month_profits['shop1'];
        $montpB = $month_profits['shop2'];
        $montpC = $month_profits['shop3'];

        // Get weekly sales for each shop
        $weekly_sales = $this->weeklySales();

        // Get total sales, cash sales, credit sales
        $sales = Sales::sum('total_price');
        $cash = Sales::sum('amnt_paid');
        $credit = $sales - $cash;

        // Get today's sales, cost, expenses and profit per shop
        $todayStats = $this->getTodayStats();

        // Get monthly sales, cost, expenses and profit per shop
        $monthlyStats = $this->getMonthlyStats();

        // Get Top 5 Products by Profit for the current month
        $topProducts = $this->getTopProductsByProfit();
        
        return view('home', compact('cash', 'credit', 'weekly_sales', 'todayStats', 'monthlyStats', 'montpA', 'montpB', 'montpC','topProducts'));
    }

    public function getShopProfitPerMonth()
    {
        // Get period (last 8 months including current)
        $period = now()->subMonths(7)->monthsUntil(now());

        // ---- 1. SALES ----
        $sales = Sales::selectRaw('
                shop_id,
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(total_price) as total_sales
            ')
            ->whereBetween('created_at', [now()->subMonths(7)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('shop_id', 'year', 'month')
            ->get()
            ->keyBy(function($row) {
                return $row->shop_id . '-' . $row->year . '-' . $row->month;
            });

        // ---- 2. COST ----
        $costs = Sales::join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->selectRaw('
                sales.shop_id,
                YEAR(sales.created_at) as year,
                MONTH(sales.created_at) as month,
                ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) as total_cost
            ')
            ->whereBetween('sales.created_at', [now()->subMonths(7)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('sales.shop_id', 'year', 'month')
            ->get()
            ->keyBy(function($row) {
                return $row->shop_id . '-' . $row->year . '-' . $row->month;
            });

        // ---- 3. EXPENSE ----
        $expenses = Expense::selectRaw('
                shop_id,
                YEAR(created_at) as year,
                MONTH(created_at) as month,
                SUM(amount) as total_expenses
            ')
            ->whereBetween('created_at', [now()->subMonths(7)->startOfMonth(), now()->endOfMonth()])
            ->groupBy('shop_id', 'year', 'month')
            ->get()
            ->keyBy(function($row) {
                return $row->shop_id . '-' . $row->year . '-' . $row->month;
            });

        // ---- 4. BUILD RESULTS ----
        $shops = [1, 2, 3]; // shop IDs
        $results = [];

        foreach ($shops as $shop) {
            $shopData = [];
            foreach ($period as $date) {
                $key = $shop . '-' . $date->year . '-' . $date->month;

                $salesVal   = $sales[$key]->total_sales ?? 0;
                $costVal    = $costs[$key]->total_cost ?? 0;
                $expenseVal = $expenses[$key]->total_expenses ?? 0;

                $shopData[] = [
                    'month'  => $date->shortMonthName,
                    'year'   => $date->year,
                    'profit' => $salesVal - $costVal - $expenseVal,
                ];
            }
            $results["shop{$shop}"] = $shopData;
        }

        return $results;
    }

    public function weeklySales()
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        // Fetch all sales for the week across all shops in one query
        $sales = Sales::selectRaw('shop_id, DAYOFWEEK(created_at) as day, SUM(total_price) as total')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->groupBy('shop_id', 'day')
            ->get();

        // Initialize array for all shops & days
        $shops = [1, 2, 3]; // or fetch dynamically from DB
        $days = collect(range(1,7))->mapWithKeys(function($d) {
            // MySQL DAYOFWEEK returns 1=Sunday, 2=Monday...7=Saturday
            // Map to proper labels if needed
            return [$d => 0];
        });

        $result = [];

        foreach ($shops as $shop) {
            // Start with all days = 0
            $result[$shop] = $days->toArray();

            // Fill in actual totals
            foreach ($sales->where('shop_id', $shop) as $row) {
                $result[$shop][$row->day] = $row->total;
            }
        }

        return $result;
    }

    public function getTodayStats()
    {
        $today = Carbon::today()->toDateString();

        // 1) Sales per shop
        $sales = Sales::whereDate('sales.created_at', $today)
            ->selectRaw('shop_id, SUM(sales.total_price) as total_sales')
            ->groupBy('shop_id')
            ->pluck('total_sales', 'shop_id');

        // 2) Cost per shop
        $costs = Sales::join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereDate('sales.created_at', $today)
            ->selectRaw('sales.shop_id, ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) as total_cost')
            ->groupBy('sales.shop_id')
            ->pluck('total_cost', 'sales.shop_id');

        // 3) Expenses per shop
        $expenses = Expense::whereDate('expenses.created_at', $today)
            ->selectRaw('shop_id, SUM(expenses.amount) as total_expenses')
            ->groupBy('shop_id')
            ->pluck('total_expenses', 'shop_id');

        // Merge results
        $results = [];
        $shopIds = array_unique(array_merge(
            $sales->keys()->toArray(),
            $costs->keys()->toArray(),
            $expenses->keys()->toArray()
        ));

        foreach ([1, 2, 3] as $shopId) {
            $s = $sales[$shopId] ?? 0;
            $c = $costs[$shopId] ?? 0;
            $e = $expenses[$shopId] ?? 0;

            $results[$shopId] = [
                'sales'    => $s,
                'cost'     => $c,
                'expenses' => $e,
                'profit'   => $s - $c - $e,
            ];
        }

        return $results;
    }

    public function getMonthlyStats()
    {
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;

        $shops = [1, 2, 3]; // you can expand later
        $monthlyStats = [];

        foreach ($shops as $shopId) {
            $sales = Sales::selectRaw('SUM(sales.total_price) as total_sales')
                ->whereYear('sales.created_at', $year)
                ->whereMonth('sales.created_at', $month)
                ->where('shop_id', $shopId)
                ->value('total_sales') ?? 0;

            $cost = Sales::selectRaw('ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) as total_cost')
                ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
                ->whereYear('sales.created_at', $year)
                ->whereMonth('sales.created_at', $month)
                ->where('shop_id', $shopId)
                ->value('total_cost') ?? 0;

            $expenses = Expense::selectRaw('SUM(expenses.amount) as total_expenses')
                ->whereYear('expenses.created_at', $year)
                ->whereMonth('expenses.created_at', $month)
                ->where('shop_id', $shopId)
                ->value('total_expenses') ?? 0;

            $profit = $sales - $cost - $expenses;

            $monthlyStats[$shopId] = [
                'sales' => $sales,
                'cost' => $cost,
                'expenses' => $expenses,
                'profit' => $profit,
            ];
        }

        return $monthlyStats;
    }

    /**
     * Get Top 5 Products by Profit for the current month
     */
    public function getTopProductsByProfit($limit = 5)
    {
        return Sales::select(
                'products.name as product_name',
                DB::raw('SUM(sales.total_price) as total_sales'),
                DB::raw('ROUND(SUM(sales.units/stocks.units * (stocks.pcost + stocks.ccost + stocks.tcost)), 2) as total_cost'),
                DB::raw('SUM(sales.total_price) - ROUND(SUM(sales.units/stocks.units * (stocks.pcost + stocks.ccost + stocks.tcost)), 2) as profit')
            )
            ->join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->whereYear('sales.created_at', Carbon::now()->year)
            ->whereMonth('sales.created_at', Carbon::now()->month)
            ->groupBy('products.name')
            ->orderByDesc('profit')
            ->take($limit)
            ->get();
    }

}
