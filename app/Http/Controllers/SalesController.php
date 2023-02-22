<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sales;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller {

    public function index() {
        //$product = Product::all()->sortBy('name')->values();
        $stock = Stock::all()->sortBy('product.name')->where('batch', '!=', 0)->where('soldout', 0)->values();
        $customer = Customer::all()->sortBy('name')->values();
        return view('pages/sales/index', compact('stock', 'customer'));
    }

    public function updateStockSales(){
        $product = Product::all()->sortBy('name')->values();
        $query = "";
        for($i=1; $i<=count($product); $i++){
            $stock = DB::table('stocks')->select('id')->where('product_id', $i);
            /*$query = DB::table('sales')
                ->where('product_id', $i)
                ->update(['product_id' => DB::raw('SELECT id FROM stocks WHERE product_id = '.$i.' ORDER BY id LIMIT 1')]);*/
            $query = DB::update(DB::raw('UPDATE sales SET stock_id = (SELECT id FROM stocks WHERE product_id = '.$i.' ORDER BY id LIMIT 1) WHERE product_id = '.$i));
        }
        if($query){
            return true;
        } else {
            return false;
        }
    }

    public function getData() {
        $data = Sales::all()->sortByDesc('created_at')->take(50)->values();

        return datatables()->of($data)
                        ->addColumn('product', function ($data) {
                            $product = $data->stock->product->name;
                            $batch = $data->stock->batch;
                            $prd = $batch." - ".$product;
                            return isset($prd) ? $prd : '';
                        })
                        ->addColumn('customer', function ($data) {
                            return isset($data->customer->name) ? $data->customer->name : '';
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

    public function store(Request $req) {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'stock_id' => 'required|max:50',
            'units' => 'required|max:20',
            'price' => 'required|max:20',
            'customer_id' => 'required',
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
        $pdt = DB::select(DB::raw("SELECT product_id FROM stocks WHERE id = $stock_id"));
        $product_id = $pdt[0]->product_id;
        $data_input['product_id'] = $product_id;
        $data_input['user_id'] = auth()->user()->id;
        $data_input['total_price'] = $req->units * $req->price;

        $sale = Sales::updateOrCreate(['id' => $id], $data_input);
        $paid = $req->amnt_paid;
        $module = "sale";
        $item_id = $sale->id;
        $cash = (new CashController())->calculateCash($module,$item_id,$action_type,$paid,$data_input['user_id']);

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

    public function destroy($id) {
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
