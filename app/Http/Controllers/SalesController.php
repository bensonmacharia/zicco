<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesController extends Controller {

    public function index() {
        $product = Product::all()->sortBy('name')->values();
        $customer = Customer::all()->sortBy('name')->values();
        return view('pages/sales/index', compact('product', 'customer'));
    }

    public function getData() {
        $data = Sales::all()->sortByDesc('updated_at')->values();

        return datatables()->of($data)
                        ->addColumn('product', function ($data) {
                            return isset($data->product->name) ? $data->product->name : '';
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
                            return isset($data->updated_at) ? $data->updated_at : '';
                        })
                        ->addIndexColumn()
                        ->make(true);
    }

    public function store(Request $req) {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'product_id' => 'required|max:50',
            'units' => 'required|max:20',
            'price' => 'required|max:20',
            'customer_id' => 'required',
            'amnt_paid' => 'required',
            'rcpt_no' => 'max:20',
            'inv_no' => 'max:20',
        ]);

        $data_input = $req->all();
        if ($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }

        $data_input['price'] = str_replace('.', '', $data_input['price']);
        $data_input['user_id'] = auth()->user()->id;
        $data_input['total_price'] = $req->units * $req->price;

        $sale = Sales::updateOrCreate(['id' => $id], $data_input);

        if ($sale) {
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
