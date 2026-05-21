<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\Expense;
use App\Models\Sales;
use App\Models\Stock;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{

    public function index()
    {
        $shop = Shop::all()->sortBy('id')->values();
        $stock = Stock::all()->sortBy('product.name')->where('batch', '!=', 0)->where('soldout', 0)->values();
        $customer = Customer::all()->sortBy('name')->values();
        $seller = Seller::all()->sortBy('id')->values();
        return view('pages/sales/index', compact('shop', 'stock', 'customer', 'seller'));
    }

    public function updateStockSales()
    {
        $product = Product::all()->sortBy('name')->values();
        $query = "";
        for ($i = 1; $i <= count($product); $i++) {
            $query = DB::update('UPDATE sales SET stock_id = (SELECT id FROM stocks WHERE product_id = ? ORDER BY id LIMIT 1) WHERE product_id = ?', [$i, $i]);
        }
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function getData()
    {
        $data = Sales::all()->sortByDesc('created_at')->take(50)->values();

        return datatables()->of($data)
            ->addColumn('product', function ($data) {
                $product = $data->stock->product->name;
                $batch = $data->stock->batch;
                $prd = $batch . " - " . $product;
                return isset($prd) ? $prd : '';
            })
            ->addColumn('customer', function ($data) {
                return isset($data->customer->name) ? $data->customer->name : '';
            })
            ->addColumn('seller', function ($data) {
                return isset($data->seller->name) ? $data->seller->name : '';
            })
            ->addColumn('shop', function ($data) {
                return isset($data->shop->name) ? $data->shop->name : '';
            })
            ->addColumn('paid', function ($data) {
                return $data->amnt_paid ? 'KES. ' . number_format($data->amnt_paid, 0, ',', ',') : '';
            })
            ->addColumn('balance', function ($data) {
                $balance = $data->total_price - $data->amnt_paid;
                return $balance ? 'KES. ' . number_format($balance, 0, ',', ',') : '';
            })
            ->addColumn('total_price', function ($data) {
                return $data->total_price ? 'KES. ' . number_format($data->total_price, 0, ',', ',') : '';
            })
            ->addColumn('added_by', function ($data) {
                return isset($data->user->username) ? $data->user->username : '';
            })
            ->addColumn('date_added', function ($data) {
                $date = date('d-M-Y', strtotime($data->created_at));
                return $date;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req)
    {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'stock_id' => 'required|max:50',
            'units' => 'required|max:20',
            'price' => 'required|max:20',
            'customer_id' => 'required',
            'seller_id' => 'required',
            'shop_id' => 'required',
            'amnt_paid' => 'required',
            'rcpt_no' => 'max:20',
            'inv_no' => 'max:20',
        ]);

        $data_input = $req->all();
        if ($id) {
            $action_type = 1;
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $action_type = 0;
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }

        $data_input['price'] = str_replace('.', '', $data_input['price']);
        $stock_id = $data_input['stock_id'];
        $pdt = DB::select("SELECT product_id FROM stocks WHERE id = ?", [$stock_id]);
        $product_id = $pdt[0]->product_id;
        $data_input['product_id'] = $product_id;
        $data_input['user_id'] = auth()->user()->id;
        $data_input['total_price'] = $req->units * $req->price;

        $sale = Sales::updateOrCreate(['id' => $id], $data_input);
        $paid = $req->amnt_paid;
        $module = "sale";
        $item_id = $sale->id;
        $cash = (new CashController())->calculateCash($module, $item_id, $action_type, $paid, $data_input['user_id']);

        if ($sale && $cash) {
            $message = array();
            $message['message'] = 'Data saved successfully';

            return response()->json($message)->setStatusCode(200);
        } else {

            $message = array();
            $message['message'] = 'Data failed to save';

            return response()->json($message)->setStatusCode(400);
        }
    }

    public function reports()
    {
        $selectdDate = Carbon::today()->toDateString();
        $displayDate = Carbon::parse($selectdDate)->format('d-M-Y');
        $displayMonth = Carbon::now()->format('F Y');
        $sellers = Seller::all();

        // Get date sales, cost, expenses and profit per shop
        $dateStats = $this->getDateStats($selectdDate);

        // Get month sales, cost, expenses and profit per shop
        $monthStats = $this->getMonthStats(Carbon::now()->month, Carbon::now()->year);

        return view('pages/sales/reports', compact('displayDate', 'displayMonth', 'dateStats', 'monthStats', 'sellers'));
    }

    public function salesReportByDate(Request $request)
    {
        $selectedDate = $request->input('drSizeMd');

        if (!$selectedDate) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $dateStats = $this->getDateStats($selectedDate);
        $displayDate = \Carbon\Carbon::parse($selectedDate)->format('d-M-Y');

        $sellerStats = $this->getSellerDateStats($selectedDate);

        $sellers = Seller::all();

        $sellerResponse = [
            'sales' => [],
            'cost' => [],
            'profit' => [],
        ];

        foreach ($sellers as $seller) {

            $sellerResponse['sales'][] =
                number_format(
                    $sellerStats[$seller->id]['sales'] ?? 0
                );

            $sellerResponse['cost'][] =
                number_format(
                    $sellerStats[$seller->id]['cost'] ?? 0
                );

            $sellerResponse['profit'][] =
                number_format(
                    $sellerStats[$seller->id]['profit'] ?? 0
                );
        }

        return response()->json([
            'displayDate' => $displayDate,
            'stats' => [
                'sales'    => [
                    number_format($dateStats[1]['sales']),
                    number_format($dateStats[2]['sales']),
                    number_format($dateStats[3]['sales']),
                ],
                'cost'     => [
                    number_format($dateStats[1]['cost']),
                    number_format($dateStats[2]['cost']),
                    number_format($dateStats[3]['cost']),
                ],
                'expenses' => [
                    number_format($dateStats[1]['expenses']),
                    number_format($dateStats[2]['expenses']),
                    number_format($dateStats[3]['expenses']),
                ],
                'profit'   => [
                    number_format($dateStats[1]['profit']),
                    number_format($dateStats[2]['profit']),
                    number_format($dateStats[3]['profit']),
                ],
            ],
            'sellerStats' => $sellerResponse,
        ]);
    }

    public function sellerReportByDate(Request $request)
    {
        $selectedDate = $request->input('sellerDate');

        if (!$selectedDate) {
            return response()->json([
                'error' => 'Date is required'
            ], 400);
        }

        $stats = $this->getSellerDateStats($selectedDate);

        $displayDate = Carbon::parse($selectedDate)
            ->format('d-M-Y');

        $sellers = Seller::all();

        $response = [
            'sales' => [],
            'cost' => [],
            'profit' => [],
        ];

        foreach ($sellers as $seller) {

            $response['sales'][] =
                number_format(
                    $stats[$seller->id]['sales'] ?? 0
                );

            $response['cost'][] =
                number_format(
                    $stats[$seller->id]['cost'] ?? 0
                );

            $response['profit'][] =
                number_format(
                    $stats[$seller->id]['profit'] ?? 0
                );
        }

        return response()->json([
            'displayDate' => $displayDate,
            'stats' => $response,
        ]);
    }

    public function salesReportByMonth(Request $request)
    {
        $month = $request->input('month');
        $year = now()->year;

        if (!$month) {
            return response()->json(['error' => 'Month is required'], 400);
        }

        $stats = $this->getMonthStats($month, $year);
        $sellerMonthStats = $this->getSellerMonthStats($month, $year);

        $sellers = Seller::all();

        $sellerMonthResponse = [
            'sales' => [],
            'cost' => [],
            'profit' => [],
        ];

        foreach ($sellers as $seller) {

            $sellerMonthResponse['sales'][] =
                number_format(
                    $sellerMonthStats[$seller->id]['sales'] ?? 0
                );

            $sellerMonthResponse['cost'][] =
                number_format(
                    $sellerMonthStats[$seller->id]['cost'] ?? 0
                );

            $sellerMonthResponse['profit'][] =
                number_format(
                    $sellerMonthStats[$seller->id]['profit'] ?? 0
                );
        }

        return response()->json([
            'displayMonth' => Carbon::createFromDate($year, $month, 1)->format('F Y'),
            'stats' => $stats,
            'sellerMonthStats' => $sellerMonthResponse,
        ]);
    }

    public function getDateStats($selectdDate)
    {
        // 1) Sales per shop
        $sales = Sales::whereDate('sales.created_at', $selectdDate)
            ->selectRaw('shop_id, SUM(sales.total_price) as total_sales')
            ->groupBy('shop_id')
            ->pluck('total_sales', 'shop_id');

        // 2) Cost per shop
        $costs = Sales::join('stocks', 'stocks.id', '=', 'sales.stock_id')
            ->whereDate('sales.created_at', $selectdDate)
            ->selectRaw('sales.shop_id, ROUND(SUM(sales.units/stocks.units*(stocks.pcost+stocks.ccost+stocks.tcost)), 0) as total_cost')
            ->groupBy('sales.shop_id')
            ->pluck('total_cost', 'sales.shop_id');

        // 3) Expenses per shop
        $expenses = Expense::whereDate('expenses.created_at', $selectdDate)
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

    public function getSellerDateStats($selectedDate)
    {
        $sales = Sales::whereDate(
            'sales.created_at',
            $selectedDate
        )
            ->selectRaw('
            seller_id,
            SUM(sales.total_price) as total_sales
        ')
            ->groupBy('seller_id')
            ->pluck('total_sales', 'seller_id');

        $costs = Sales::join(
            'stocks',
            'stocks.id',
            '=',
            'sales.stock_id'
        )
            ->whereDate(
                'sales.created_at',
                $selectedDate
            )
            ->selectRaw('
            sales.seller_id,

            ROUND(
                SUM(
                    sales.units / stocks.units *
                    (
                        stocks.pcost +
                        stocks.ccost +
                        stocks.tcost
                    )
                ),
            0) as total_cost
        ')
            ->groupBy('sales.seller_id')
            ->pluck('total_cost', 'seller_id');

        $results = [];

        $sellers = Seller::all();

        foreach ($sellers as $seller) {

            $s = $sales[$seller->id] ?? 0;

            $c = $costs[$seller->id] ?? 0;

            $results[$seller->id] = [
                'sales' => $s,
                'cost' => $c,
                'profit' => $s - $c,
            ];
        }

        return $results;
    }

    public function getMonthStats($month, $year)
    {

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

    public function getSellerMonthStats($month, $year)
    {
        $results = [];

        $sellers = Seller::all();

        foreach ($sellers as $seller) {

            $sales = Sales::whereYear(
                'sales.created_at',
                $year
            )
                ->whereMonth(
                    'sales.created_at',
                    $month
                )
                ->where(
                    'seller_id',
                    $seller->id
                )
                ->sum('sales.total_price');

            $cost = Sales::join(
                'stocks',
                'stocks.id',
                '=',
                'sales.stock_id'
            )
                ->whereYear(
                    'sales.created_at',
                    $year
                )
                ->whereMonth(
                    'sales.created_at',
                    $month
                )
                ->where(
                    'seller_id',
                    $seller->id
                )
                ->selectRaw('
                ROUND(
                    SUM(
                        sales.units / stocks.units *
                        (
                            stocks.pcost +
                            stocks.ccost +
                            stocks.tcost
                        )
                    ),
                0) as total_cost
            ')
                ->value('total_cost') ?? 0;

            $results[$seller->id] = [
                'sales' => $sales,
                'cost' => $cost,
                'profit' => $sales - $cost,
            ];
        }

        return $results;
    }

    public function destroy($id)
    {
        $sale = Sales::where('id', $id)->first();

        if ($sale->delete()) {
            $message = array();
            $message['message'] = 'Data deleted successfully';

            return response()->json($message)->setStatusCode(200);
        } else {

            $message = array();
            $message['message'] = 'Data failed to delete';

            return response()->json($message)->setStatusCode(400);
        }
    }
}
