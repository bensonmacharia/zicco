<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;

class StockController extends Controller
{
    public function index() {
        $product = Product::all();
        return view('pages/stock/index', compact('product'));
    }
    
    public function getData() {
        $data = Stock::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
        ->addColumn('product_name', function ($data) {
            return isset($data->product->name)? $data->product->name : '';
        })
        ->addColumn('total_cost', function ($data) {
            return $data->cost ? 'KES. '.number_format($data->cost, 0, ',', '.') : '';
        })
        ->addColumn('added_by', function ($data) {
            return isset($data->user->username)? $data->user->username : '';
        })
        ->addColumn('date_added', function ($data) {
            return isset($data->created_at)? $data->created_at : '';
        })
        ->addIndexColumn()
        ->make(true);
    }
    
    public function store(Request $req){
        $id = $req->id?:0;
        $validated = $req->validate([
            'product_id' => 'required',
            'units' => 'required|max:50',
            'cost' => 'required',
        ]);
        $data_input = $req->all();
        if($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }
        $data_input['cost'] = str_replace('.', '', $data_input['cost']);
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
