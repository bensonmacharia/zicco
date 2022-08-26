<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use File;

class AdminController extends Controller {

    public function index() {
        //$customer = Customer::all()->sortByDesc('created_at')->values();
        return view('pages/admin/index');
    }

    public function guide() {
        //$customer = Customer::all()->sortByDesc('created_at')->values();
        return view('pages/admin/guide');
    }

    public function getData() {
        $data = Customer::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
                        ->addColumn('added_by', function ($data) {
                            return isset($data->user->username) ? $data->user->username : '';
                        })
                        ->addColumn('date_added', function ($data) {
                            return isset($data->created_at) ? $data->created_at : '';
                        })
                        ->addColumn('cust_status', function ($data) {
                            $stat = $data->status_id;
                            return $stat === 1 ? "Active" : "Inactive";
                        })
                        ->addIndexColumn()
                        ->make(true);
    }

    public function store(Request $req) {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'name' => 'required|unique:products|max:50',
            'email' => 'max:50',
            'phone' => 'max:50',
            'status_id' => 'required',
        ]);

        $data_input = $req->all();
        if ($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }
        $data_input['user_id'] = auth()->user()->id;

        $customer = Customer::updateOrCreate(['id' => $id], $data_input);

        if ($customer) {
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
