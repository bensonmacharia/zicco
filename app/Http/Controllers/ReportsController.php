<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sales;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        $product = Product::all()->sortBy('name')->values();
        $customer = Customer::all()->sortBy('name')->values();
        return view('pages/reports/index', compact('product', 'customer'));
    }

    public function customer_sales()
    {
        $product = Product::all()->sortBy('name')->values();
        $customer = Customer::all()->sortBy('name')->values();
        return view('pages/reports/customer_sales', compact('product', 'customer'));
    }

    public function debtors()
    {
        $product = Product::all()->sortBy('name')->values();
        $customer = Customer::all()->sortBy('name')->values();
        return view('pages/reports/debtors', compact('product', 'customer'));
    }

    public function getDebtors()
    {
        $data = Sales::whereColumn('total_price', '!=', 'amnt_paid')->get()->sortBy('customer_id');

        return datatables()->of($data)
            ->addColumn('product', function ($data) {
                $product = $data->stock->product->name;
                $batch = $data->stock->batch;
                return $batch . " - " . $product;
            })
            ->addColumn('customer', function ($data) {
                return isset($data->customer->name) ? $data->customer->name : '';
            })
            ->addColumn('paid', function ($data) {
                return $data->amnt_paid;
            })
            ->addColumn('profit', function ($data) {
                $total_price = $data->total_price;
                $total_cost = $data->stock->pcost + $data->stock->ccost + $data->stock->tcost;
                $sale_cost = ($total_cost / $data->stock->units) * $data->units;
                $profit = $total_price - $sale_cost;
                //return (float)number_format((float)$profit, 0, '.', '');
                return round($profit);
            })
            ->addColumn('balance', function ($data) {
                $balance = $data->total_price - $data->amnt_paid;
                return $balance;
            })
            ->addColumn('total_price', function ($data) {
                return $data->total_price;
            })
            ->addColumn('date_added', function ($data) {
                $date = date('d-M-Y', strtotime($data->created_at));
                return $date;
            })
            ->addColumn('time_ago', function ($data) {
                $dateAgo = $data->created_at->diffForHumans();
                return $dateAgo;
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function today()
    {
        //$data = Sales::all()->sortByDesc('created_at')->values();
        $now = Carbon::now();
        $data = Sales::whereDate('created_at', $now->format('Y-m-d'))->get();

        return datatables()->of($data)
            ->addColumn('product', function ($data) {
                $product = $data->stock->product->name;
                $batch = $data->stock->batch;
                return $batch . " - " . $product;
            })
            ->addColumn('customer', function ($data) {
                return isset($data->customer->name) ? $data->customer->name : '';
            })
            ->addColumn('paid', function ($data) {
                return $data->amnt_paid;
            })
            ->addColumn('profit', function ($data) {
                $total_price = $data->total_price;
                $total_cost = $data->stock->pcost + $data->stock->ccost + $data->stock->tcost;
                $sale_cost = ($total_cost / $data->stock->units) * $data->units;
                $profit = $total_price - $sale_cost;
                return round($profit);
            })
            ->addColumn('balance', function ($data) {
                $balance = $data->total_price - $data->amnt_paid;
                return $balance;
            })
            ->addColumn('total_price', function ($data) {
                return isset($data->total_price) ? $data->total_price : '';
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

    public function salesByDate($start, $end)
    {

        $data = Sales::whereBetween('created_at', [$start . " 00:00:00", $end . " 23:59:59"])->get();

        return datatables()->of($data)
            ->addColumn('product', function ($data) {
                $product = $data->stock->product->name;
                $batch = $data->stock->batch;
                return $batch . " - " . $product;
            })
            ->addColumn('customer', function ($data) {
                return isset($data->customer->name) ? $data->customer->name : '';
            })
            ->addColumn('paid', function ($data) {
                return $data->amnt_paid;
            })
            ->addColumn('profit', function ($data) {
                $total_price = $data->total_price;
                $total_cost = $data->stock->pcost + $data->stock->ccost + $data->stock->tcost;
                $sale_cost = ($total_cost / $data->stock->units) * $data->units;
                $profit = $total_price - $sale_cost;
                return round($profit);
            })
            ->addColumn('balance', function ($data) {
                $balance = $data->total_price - $data->amnt_paid;
                return $balance;
            })
            ->addColumn('total_price', function ($data) {
                $units = $data->units;
                $price = $data->price;
                $total = $units * $price;
                return $total;
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

    public function salesByCustomer($customer)
    {
        $data = Sales::where('customer_id', $customer)->get();

        return datatables()->of($data)
            ->addColumn('product', function ($data) {
                $product = $data->stock->product->name;
                $batch = $data->stock->batch;
                return $batch . " - " . $product;
            })
            ->addColumn('customer', function ($data) {
                return isset($data->customer->name) ? $data->customer->name : '';
            })
            ->addColumn('paid', function ($data) {
                return $data->amnt_paid;
            })
            ->addColumn('profit', function ($data) {
                $total_price = $data->total_price;
                $total_cost = $data->stock->pcost + $data->stock->ccost + $data->stock->tcost;
                $sale_cost = ($total_cost / $data->stock->units) * $data->units;
                $profit = $total_price - $sale_cost;
                return round($profit);
                //return (float)number_format((float)$profit, 0, '.', '');
            })
            ->addColumn('balance', function ($data) {
                $units = $data->units;
                $price = $data->price;
                $total = $units * $price;
                $balance = $total - $data->amnt_paid;
                return $balance;
            })
            ->addColumn('total_price', function ($data) {
                $units = $data->units;
                $price = $data->price;
                $total = $units * $price;
                return $total;
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
}
