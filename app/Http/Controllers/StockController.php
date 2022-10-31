<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use App\Models\Product;
use App\Models\Order;
use App\Models\Stock;

class StockController extends Controller {

    public function index() {
        $product = Product::all()->sortBy('name')->values();
        $order = Order::all()->sortBy('id')->values();
        return view('pages/stock/index', compact('product', 'order'));
    }

    public function summary() {
        $product = Product::all()->sortBy('name')->values();
        return view('pages/stock/summary', compact('product'));
    }

    public function load_stock_summary() {
        $subQuery = Stock::groupBy('stocks.product_id')
                ->join('products', 'products.id', '=', 'stocks.product_id')
                ->select('products.id', 'products.name as product_name')
                ->selectRaw('SUM(stocks.units) as total_units');

        $data = Sales::select(['ps.id as product_id', 'ps.product_name', 'ps.total_units'])
                ->joinSub($subQuery->toSql(), 'ps', function ($join) {
                    $join->on('ps.id', '=', 'sales.product_id');
                })
                ->selectRaw('SUM(sales.units) total_sold')
                ->selectRaw('SUM(sales.total_price) total_sales')
                ->selectRaw('SUM(sales.amnt_paid) total_paid')
                ->groupBy('ps.id')
                ->orderBy('ps.product_name', 'asc')
                ->get();


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

    public function soldout(){
        $product = Product::all()->sortBy('name')->values();
        $order = Order::all()->sortBy('id')->values();
        return view('pages/stock/soldout', compact('product', 'order'));
    }

    public function getSoldOut() {
        $data = Stock::all()->sortByDesc('created_at')->where('soldout', 1)->values();

        return datatables()->of($data)
            ->addColumn('product_name', function ($data) {
                return isset($data->product->name) ? $data->product->name : '';
            })
            ->addColumn('total_cost', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                return 'KES. ' . number_format($total, 0, ',', ',');
            })
            ->addColumn('total_sales', function ($data) {
                return $data->order->esale ? 'KES. ' . number_format($data->order->esale, 0, ',', ',') : '';
            })
            ->addColumn('profit', function ($data) {
                $total = $data->pcost + $data->ccost + $data->tcost;
                $profit = $data->order->esale -$total;
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

    public function getData() {
        $data = Stock::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
                        ->addColumn('product_name', function ($data) {
                            return isset($data->product->name) ? $data->product->name : '';
                        })
                        ->addColumn('total_cost', function ($data) {
                            $total = $data->pcost + $data->ccost + $data->tcost;
                            return 'KES. ' . number_format($total, 0, ',', ',');
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
