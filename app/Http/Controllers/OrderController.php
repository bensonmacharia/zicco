<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Order;

class OrderController extends Controller {

    public function index() {
        $product = Product::all()->sortBy('name')->values();
        $partner = Partner::all()->sortBy('id')->values();
        return view('pages/orders/index', compact('product', 'partner'));
    }


    public function getData() {
        $data = Order::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
                        ->addColumn('product_name', function ($data) {
                            return isset($data->product->name) ? $data->product->name : '';
                        })
                        ->addColumn('batch', function ($data) {
                            return isset($data->batch) ? $data->batch : '';
                        })
                        ->addColumn('units', function ($data) {
                            return isset($data->units) ? $data->units : '';
                        })
                        ->addColumn('ppcost', function ($data) {
                            return $data->pcost ? 'KES. ' . number_format($data->pcost, 0, ',', ',') : '';
                        })
                        ->addColumn('cccost', function ($data) {
                            return $data->ccost ? 'KES. ' . number_format($data->ccost, 0, ',', ',') : '';
                        })
                        ->addColumn('ttcost', function ($data) {
                            return $data->tcost ? 'KES. ' . number_format($data->tcost, 0, ',', ',') : '';
                        })
                        ->addColumn('scost', function ($data) {
                            $total = $data->pcost + $data->ccost + $data->tcost;
                            return 'KES. ' . number_format($total, 0, ',', ',');
                        })
                        ->addColumn('eprofit', function ($data) {
                            $total_cost = $data->pcost + $data->ccost + $data->tcost;
                            $eprofit = $data->esale - $total_cost;
                            return $eprofit;
                        })
                        ->addColumn('expense', function ($data) {
                            $total_cost = $data->pcost + $data->ccost + $data->tcost;
                            $eprofit = $data->esale - $total_cost;
                            return (10 / 100) * $eprofit;
                        })
                        ->addColumn('nprofit', function ($data) {
                            $total_cost = $data->pcost + $data->ccost + $data->tcost;
                            $eprofit = $data->esale - $total_cost;
                            return (90 / 100) * $eprofit;
                        })
                        ->addColumn('profitability', function ($data) {
                            $total_cost = $data->pcost + $data->ccost + $data->tcost;
                            $eprofit = $data->esale - $total_cost;
                            $nprofit = (90 / 100) * $eprofit;;
                            return ($nprofit / $data->esale) * 100;
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

    public function getOrder($id) {
        $data = Order::where('id', $id)->get();
        return $data;
    }

    public function store(Request $req) {
        $id = $req->id ?: 0;
        $validated = $req->validate([
            'product_id' => 'required',
            'payment' => 'required',
            'batch' => 'required|max:10',
            'units' => 'required|max:50',
            'pcost' => 'required',
            'ccost' => 'required',
            'tcost' => 'required',
            'esale' => 'required',
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
        $data_input['esale'] = str_replace('.', '', $data_input['esale']);
        $data_input['user_id'] = auth()->user()->id;

        $order = Order::updateOrCreate(['id' => $id], $data_input);

        $message = array();
        if ($order) {
            $message['message'] = 'Order recorded successfully';

            return response()->json($message)->setStatusCode(200);
        } else {
            $message['message'] = 'Order data failed to save';

            return response()->json($message)->setStatusCode(400);
        }
    }

    public function payment(Request $req) {
        $validated = $req->validate([
            'order_id' => 'required',
        ]);
        $data_input = $req->all();
    }

}
