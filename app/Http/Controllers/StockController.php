<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Order;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class StockController extends Controller {

    public function index() {
        $product = Product::all()->sortBy('name')->values();
        $order = Order::all()->sortBy('product.name')->values();
        return view('pages/stock/index', compact('product', 'order'));
    }

    public function summary() {
        $product = Product::all()->sortBy('name')->values();
        return view('pages/stock/summary', compact('product'));
    }

    public function aggregate() {
        $product = Product::all()->sortBy('name')->values();
        return view('pages/stock/aggregate', compact('product'));
    }

    public function load_stock_aggregate() {
        $subQuery = Stock::select('products.id as product_id', 'products.name as product_name')
            ->selectRaw('SUM(stocks.units) as total_units')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->groupBy('products.id')
            ->groupBy('products.name');

        $data = Sales::select('ps.product_id', 'ps.product_name','ps.total_units')
            ->join('products', 'products.id', '=', 'sales.product_id')
            ->joinSub($subQuery->toSql(), 'ps', function ($join) {
                $join->on('ps.product_id', '=', 'sales.product_id');
            })
            ->selectRaw('SUM(sales.units) total_sold')
            ->selectRaw('SUM(sales.total_price) total_sales')
            ->selectRaw('SUM(sales.amnt_paid) total_paid')
            ->groupBy('ps.product_id')
            ->get();

        /*return datatables()->of($data)
            ->addIndexColumn()
            ->make(true);*/


        return datatables()->of($data)
                        ->addColumn('product_id', function ($data) {
                            return isset($data->product_id) ? $data->product_id : '';
                        })
                        ->addColumn('product_name', function ($data) {
                            return isset($data->product_name) ? $data->product_name : '';
                        })
                        ->addColumn('total_units', function ($data) {
                            return isset($data->total_units) ? number_format($data->total_units, 0, ',', ',') : '';
                        })
                        ->addColumn('total_sold', function ($data) {
                            return isset($data->total_sold) ? number_format($data->total_sold, 0, ',', ',') : '';
                        })
                        ->addColumn('total_remaining', function ($data) {
                            $all = $data->total_units;
                            $sold = $data->total_sold;
                            $balance = $all - $sold;
                            return number_format($balance, 0, ',', ',');
                        })
                        ->addColumn('total_sales', function ($data) {
                            return $data->total_sales ? 'KES. ' . number_format($data->total_sales, 0, ',', ',') : '';
                        })
                        ->addColumn('total_paid', function ($data) {
                            return $data->total_paid ? 'KES. ' . number_format($data->total_paid, 0, ',', ',') : '';
                        })
                        ->addColumn('total_balance', function ($data) {
                            $sales = $data->total_sales;
                            $paid = $data->total_paid;
                            $credit = $sales - $paid;
                            return 'KES. ' . number_format($credit, 0, ',', ',');
                        })
                        ->addIndexColumn()
                        ->make(true);
    }

    public function load_stock_summary() {
        $transfer = DB::table('transfers')
            ->join('stocks', 'stocks.id', '=', 'transfers.stock_id')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->select('products.name', 'stocks.id as stock_id', 'stocks.batch')
            ->selectRaw('SUM(transfers.amount) total_transferred')
            ->groupBy('transfers.stock_id');

        $summary = DB::table('stocks')
            ->join('orders', 'orders.id', '=', 'stocks.order_id')
            ->leftJoin('sales', 'sales.stock_id', '=', 'stocks.id')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->leftJoin('transfers', 'transfers.stock_id', '=', 'stocks.id')
            ->select('stocks.*','orders.esale','orders.asale','products.name as product_name')
            ->selectRaw('SUM(sales.total_price) total_sales')
            ->selectRaw('(CASE WHEN orders.asale = 0 then orders.esale else orders.asale end) as expected_sales')
            ->selectRaw('(stocks.pcost + stocks.ccost + stocks.tcost) total_cost')
            ->selectRaw('SUM(transfers.amount) total_transferred')
            ->groupBy('stocks.id');


        return datatables()->of($summary)
            ->addColumn('total_cost', function ($data) {
                return $data->total_cost ? 'KES. ' . number_format($data->total_cost, 0, ',', ',') : '';
            })
            ->addColumn('expected_sales', function ($data) {
                return $data->expected_sales ? 'KES. ' . number_format($data->expected_sales, 0, ',', ',') : '';
            })
            ->addColumn('expenditure', function ($data) {
                $expd = 0.1 * ($data->expected_sales - $data->total_cost);
                return 'KES. ' . number_format($expd, 0, ',', ',');
            })
            ->addColumn('netprofit', function ($data) {
                $expd = 0.9 * ($data->expected_sales - $data->total_cost);
                return 'KES. ' . number_format($expd, 0, ',', ',');
            })
            ->addColumn('status', function ($data) {
                $soldout = $data->soldout;
                $status = "";
                if($soldout === 1){
                    $status = "Closed";
                } else {
                    $status = "Open";
                }
                return $status;
            })
            ->addColumn('transferred', function ($data) {
                return $data->total_transferred ? 'KES. ' . number_format($data->total_transferred, 0, ',', ',') : '';
            })
            ->addColumn('remaining', function ($data) {
                $remain = $data->expected_sales - $data->total_transferred;
                return $remain ? 'KES. ' . number_format($remain, 0, ',', ',') : '';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function soldout(){
        $product = Product::all()->sortBy('name')->values();
        $order = Order::all()->sortBy('id')->values();
        return view('pages/stock/soldout', compact('product', 'order'));
    }

    public function getSoldOut() {
        //$data = Stock::all()->sortByDesc('created_at')->where('soldout', 1)->values();
        $subQuery = Sales::groupBy('sales.stock_id')
            ->select('sales.stock_id')
            ->selectRaw('SUM(sales.units) total_units')
            ->selectRaw('SUM(sales.total_price) total_sales')
            ->selectRaw('SUM(sales.total_price)/SUM(sales.units) as avg_sale');
        /*$transfer = DB::table('transfers')
            ->join('orders', 'orders.id', '=', 'transfers.order_id')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->select('transfers.stock_id as stk_id', DB::raw("GROUP_CONCAT(products.id SEPARATOR '-') as product_ids"), DB::raw("GROUP_CONCAT(products.name SEPARATOR '-') as product_names"), DB::raw("GROUP_CONCAT(orders.batch SEPARATOR '-') as batches"), DB::raw("GROUP_CONCAT(transfers.order_id SEPARATOR '-') as orders"))
            ->groupBy('transfers.stock_id');*/

        $data = Stock::select('*')
            ->joinSub($subQuery->toSql(), 'ps', function ($join) {
                $join->on('ps.stock_id', '=', 'stocks.id');
            })
            ->where('stocks.soldout', 1)
            ->orderBy('stocks.created_at', 'desc')
            ->get();

        return datatables()->of($data)
            ->addColumn('product_name', function ($data) {
                return isset($data->product->name) ? $data->product->name : '';
            })
            ->addColumn('total_cost', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                return 'KES. ' . number_format($total, 0, ',', ',');
            })
            ->addColumn('expected_sales', function ($data) {
                return $data->order->esale ? 'KES. ' . number_format($data->order->esale, 0, ',', ',') : '';
            })
            ->addColumn('actual_sales', function ($data) {
                return $data->order->asale ? 'KES. ' . number_format($data->order->asale, 0, ',', ',') : '';
            })
            ->addColumn('expected_profit', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                $profit = $data->order->esale -$total;
                return 'KES. ' . number_format($profit, 0, ',', ',');
            })
            ->addColumn('actual_profit', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                $profit = $data->order->asale -$total;
                return 'KES. ' . number_format($profit, 0, ',', ',');
            })
            ->addColumn('system_profit', function ($data) {
                $total_cost = $data->pcost + $data->ccost + $data->tcost;
                $system_sales = $data->avg_sale * $data->units;
                $profit = $system_sales - $total_cost;
                return 'KES. ' . number_format($profit, 0, ',', ',');
            })
            ->addColumn('added_by', function ($data) {
                return isset($data->user->username) ? $data->user->username : '';
            })
            ->addColumn('date_added', function ($data) {
                return isset($data->created_at) ? $data->created_at : '';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function getStockTransfers($id)
    {
        //$transfer = Transfer::all()->sortByDesc('updated_at')->values();
        $transfer = DB::table('transfers')
            ->join('orders', 'orders.id', '=', 'transfers.order_id')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->select('transfers.*','products.id as product_id','products.name as product_name','orders.batch')
            ->where('transfers.stock_id', $id)
            ->get();
        return datatables()->of($transfer)
            ->addColumn('date', function ($data) {
                return isset($data->updated_at) ? $data->updated_at : '';
            })
            ->addIndexColumn()
            ->make(true);
        //return compact('transfer');
    }

    public function getData() {
        $subQuery = Sales::groupBy('sales.stock_id')
            ->select('sales.stock_id')
            ->selectRaw('SUM(sales.units) total_units')
            ->selectRaw('SUM(sales.total_price) total_sales')
            ->selectRaw('SUM(sales.total_price)/SUM(sales.units) as avg_sale');
        $data = Stock::select('*')
            ->joinSub($subQuery->toSql(), 'ps', function ($join) {
                $join->on('ps.stock_id', '=', 'stocks.id');
            })
            ->where('stocks.soldout', 0)
            ->orderBy('stocks.created_at', 'desc')
            ->get();


        return datatables()->of($data)
            ->addColumn('product_name', function ($data) {
                return isset($data->product->name) ? $data->product->name : '';
            })
            ->addColumn('total_cost', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                return 'KES. ' . number_format($total, 0, ',', ',');
            })
            ->addColumn('expected_sales', function ($data) {
                return $data->order->esale ? 'KES. ' . number_format($data->order->esale, 0, ',', ',') : '';
            })
            ->addColumn('actual_sales', function ($data) {
                return $data->order->asale ? 'KES. ' . number_format($data->order->asale, 0, ',', ',') : '';
            })
            ->addColumn('expected_profit', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                $profit = $data->order->esale -$total;
                return 'KES. ' . number_format($profit, 0, ',', ',');
            })
            ->addColumn('actual_profit', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                $profit = $data->order->asale -$total;
                return 'KES. ' . number_format($profit, 0, ',', ',');
            })
            ->addColumn('system_profit', function ($data) {
                $total_cost = $data->pcost + $data->ccost + $data->tcost;
                $system_sales = $data->avg_sale * $data->units;
                $profit = $system_sales - $total_cost;
                return 'KES. ' . number_format($profit, 0, ',', ',');
            })
            ->addColumn('added_by', function ($data) {
                return isset($data->user->username) ? $data->user->username : '';
            })
            ->addColumn('date_added', function ($data) {
                return isset($data->created_at) ? $data->created_at : '';
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function store(Request $req) {
        $id = $req->id ?: 0;
        $validated = $req->validate([
            'order_id' => 'required',
            'soldout' => 'required',
            'product_id' => 'required',
            'batch' => 'required|max:10',
            'units' => 'required|max:50',
            'pcost' => 'required',
            'ccost' => 'required',
            'tcost' => 'required',
        ]);
        $data_input = $req->all();
        if ($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }
        $data_input['pcost'] = str_replace('.', '', $data_input['pcost']);
        $data_input['ccost'] = str_replace('.', '', $data_input['ccost']);
        $data_input['tcost'] = str_replace('.', '', $data_input['tcost']);
        $data_input['user_id'] = auth()->user()->id;

        $stock = Stock::updateOrCreate(['id' => $id], $data_input);

        if ($stock) {
            $message = array();
            $message['message'] = 'Data saved successfully';

            return response()->json($message)->setStatusCode(200);
        } else {

            $message = array();
            $message['message'] = 'Data failed to save';

            return response()->json($message)->setStatusCode(400);
        }
    }

}
