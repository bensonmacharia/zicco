<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Order;

use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $product = Product::all()->sortBy('name')->values();
        $stock = Stock::all()->sortBy('product.name')->values();
        /*$stock = DB::table('stocks')
            ->join('orders', 'orders.id', '=', 'stocks.order_id')
            ->join('products', 'products.id', '=', 'stocks.product_id')
            ->select('stocks.*','products.name','stocks.batch')
            ->get();*/
        /*$data = Order::select(['orders.*','ps1.*'])
            ->joinSub($subQuery->toSql(), 'ps1', function ($join) {
                $join->on('ps1.order_id', '=', 'orders.id');
            })
            ->orderBy('orders.created_at', 'desc')
            ->get();*/

        $partner = Partner::all()->sortBy('id')->values();
        return view('pages/orders/index', compact('product', 'partner', 'stock'));
    }

    public function shipping(){
        $product = Product::all()->sortBy('name')->values();
        $stock = Stock::all()->sortBy('product.name')->values();
        return view('pages/orders/shipping', compact('product', 'stock'));
    }

    public function getInShipment()
    {
        $data = DB::table('orders')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.*','products.name AS product_name','users.username AS added_by')
            ->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('stocks')
                    ->whereRaw('orders.product_id = stocks.product_id')
                    ->whereRaw('orders.batch = stocks.batch');
            })
            ->get();

        //$data1 = Order::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
            ->addColumn('product_name', function ($data) {
                return isset($data->product_name) ? $data->product_name : '';
            })
            ->addColumn('batch', function ($data) {
                return isset($data->batch) ? $data->batch : '';
            })
            ->addColumn('product_and_batch', function ($data) {
                $pandb = $data->batch." - ".$data->product_name;
                return $pandb;
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
            ->addColumn('added_by', function ($data) {
                return isset($data->added_by) ? $data->added_by : '';
            })
            ->addColumn('date_added', function ($data) {
                return isset($data->created_at) ? $data->created_at : '';
            })
            ->addIndexColumn()
            ->make(true);
    }


    public function getData()
    {
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
                $nprofit = (90 / 100) * $eprofit;
                if($data->esale){
                    return ($nprofit / $data->esale) * 100;
                } else {
                    return ($nprofit / 1) * 100;
                }
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

    public function getOrder($id)
    {
        $data = Order::where('id', $id)->get();
        return $data;
    }

    public function getContributions($id)
    {
        $contributions = DB::table('contributions')
            ->select('order_id',DB::raw("GROUP_CONCAT(amount SEPARATOR '-') as amounts"), DB::raw("GROUP_CONCAT(partner_id SEPARATOR '-') as partners"))
            ->groupBy('order_id')
            ->where('order_id', $id)->get();
        return $contributions;
    }

    public function getTransfers($id)
    {
        $transfers = DB::table('transfers')
            ->select('order_id',DB::raw("GROUP_CONCAT(amount SEPARATOR '-') as amounts"), DB::raw("GROUP_CONCAT(stock_id SEPARATOR '-') as products"))
            ->groupBy('order_id')
            ->where('order_id', $id)->get();
        return $transfers;
    }

    public function store(Request $req)
    {
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

        if($data_input['asale']){
            $data_input['asale'] = str_replace('.', '', $data_input['asale']);
        } else {
            $data_input['asale'] = 0;
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

    public function payment(Request $req)
    {
        parse_str($req->data, $output);
        $order_id = $output['order_id'];
        $payment_type_id = $output['payment_type_id'];
        $partner_1 = $output['partner-1'];
        $amount_1 = $output['amount-1'];
        $partner_2 = $output['partner-2'];
        $amount_2 = $output['amount-2'];
        $partner_3 = $output['partner-3'];
        $amount_3 = $output['amount-3'];

        $message = array();

        $amount_1 = str_replace('.', '', $amount_1);
        $amount_2 = str_replace('.', '', $amount_2);
        $amount_3 = str_replace('.', '', $amount_3);

        $partners = array(
            $partner_1 => $amount_1,
            $partner_2 => $amount_2,
            $partner_3 => $amount_3
        );

        $exists = Contribution::where('order_id', $order_id)->get();
        $contribution = "";
        if($exists){
            foreach ($partners as $key => $value) {
                $value = str_replace('.', '', $value);
                $data_input['updated_at'] = date('Y-m-d H:i:s');
                $data_input['amount'] = $value;
                $contribution = Contribution::updateOrCreate(['order_id' => $order_id, 'partner_id' => $key], $data_input);
            }
        } else {
            foreach ($partners as $key => $value) {
                $value = str_replace('.', '', $value);
                $data_input['created_at'] = date('Y-m-d H:i:s');
                $data_input['order_id'] = $order_id;
                $data_input['partner_id'] = $key;
                $data_input['amount'] = $value;
                $contribution = Contribution::updateOrCreate(['order_id' => $order_id, 'partner_id' => $key], $data_input);
            }
        }

        $recorded = Transfer::where('order_id', $order_id)->get();
        if (stripos(json_encode($output), 'tamount') !== false && stripos(json_encode($output), 'item') !== false) {
            $keys = array();
            $values = array();
            foreach ($output as $key => $value) {
                if (str_starts_with($key, 'item') || str_starts_with($key, 'tamount')) {
                    //echo $key." - ".$value."\n";
                    $keys[] = $key;
                    $values[] = $value;
                }
            }
            if (in_array(null, $values, true) || in_array('', $values, true)) {
                $message['message'] = 'Data recorded successfully';

                return response()->json($message)->setStatusCode(200);
            } else {
                $even = $odd = array();
                foreach ($values as $k => $v) ($k & 1) === 0 ? $even[] = $v : $odd[] = $v;
                $stocks = array();
                foreach ($even as $i => $key) {
                    $stocks[$key] = $odd[$i];
                }
                $transfers = "";
                if($recorded){
                    foreach ($stocks as $key => $value) {
                        $value = str_replace('.', '', $value);
                        $data_input['updated_at'] = date('Y-m-d H:i:s');
                        $data_input['amount'] = $value;
                        $transfers = Transfer::updateOrCreate(['order_id' => $order_id, 'stock_id' => $key], $data_input);
                    }
                } else {
                    foreach ($stocks as $key => $value) {
                        $value = str_replace('.', '', $value);
                        $data_input['created_at'] = date('Y-m-d H:i:s');
                        $data_input['order_id'] = $order_id;
                        $data_input['stock_id'] = $key;
                        $data_input['amount'] = $value;
                        $transfers = Transfer::updateOrCreate(['order_id' => $order_id, 'stock_id' => $key], $data_input);
                    }
                }

                if ($transfers) {
                    $message['message'] = 'Data recorded successfully';

                    return response()->json($message)->setStatusCode(200);
                } else {
                    $message['message'] = 'Transfer data failed to save';

                    return response()->json($message)->setStatusCode(400);
                }
            }
        } else {
            $message['message'] = 'Data recorded successfully';

            return response()->json($message)->setStatusCode(200);
        }
    }

}
