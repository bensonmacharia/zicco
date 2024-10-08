<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use App\Models\Customer;
use App\Models\Partner;
use App\Models\Shop;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller {

    public function index() {
        //$customer = Customer::all()->sortByDesc('created_at')->values();
        return view('pages/admin/index');
    }

    public function partner() {
        //$customer = Customer::all()->sortByDesc('created_at')->values();
        return view('pages/admin/partner');
    }

    public function shop() {
        return view('pages/admin/shop');
    }

    public function capital() {
        $shipment = DB::table('orders')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->selectRaw('SUM(orders.pcost) AS total_product_cost')
            ->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('stocks')
                    ->whereRaw('orders.product_id = stocks.product_id')
                    ->whereRaw('orders.batch = stocks.batch');
            })
            ->get();
        $rem_pcost = DB::select("SELECT SUM(a.rcost) as remaining_pcost FROM (SELECT ((stocks.units - stocks.spoilt - coalesce(SUM(sales.units),0)) * ((stocks.pcost+stocks.ccost+stocks.tcost)/stocks.units)) as rcost FROM stocks LEFT JOIN sales ON stocks.id = sales.stock_id WHERE stocks.soldout = 0 GROUP BY stocks.id ORDER BY stocks.id) as a");
        $spoilt_pcost = DB::select("SELECT SUM(a.spoilt_cost) as spoilt_pcost FROM (SELECT stocks.id, stocks.units, stocks.spoilt, (stocks.pcost+stocks.ccost+stocks.tcost)/stocks.units as rcost, (stocks.spoilt*((stocks.pcost+stocks.ccost+stocks.tcost)/stocks.units)) as spoilt_cost FROM stocks WHERE stocks.spoilt > 0) as a");
        $balance = collect($rem_pcost)->sum('remaining_pcost');
        $spoilt = collect($spoilt_pcost)->sum('spoilt_pcost')/2;
        //var_dump($spoilt_pcost);die();

        $laptop_date = '2022-12-21';
        //$laptop_age_years = Carbon::parse($laptop_date)->age;
        $laptop_age_months = Carbon::parse($laptop_date)->diffInMonths(Carbon::now());
        $laptop_current_value = 20000*((100 - (2*$laptop_age_months))/100);
        $laptop = $laptop_current_value;

        $cash = Cash::orderBy('id', 'desc')->first()->amount;

        $total = $shipment[0]->total_product_cost + $balance + $spoilt + $laptop + $cash;

        return view('pages/admin/capital', compact('shipment', 'balance',  'spoilt', 'laptop', 'cash', 'total'));
    }

    public function guide() {
        //$customer = Customer::all()->sortByDesc('created_at')->values();
        return view('pages/admin/guide');
    }

    public function getCapitalShare() {
        $shipment = DB::table('orders')
            ->join('products', 'products.id', '=', 'orders.product_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->selectRaw('SUM(orders.pcost) AS total_product_cost')
            ->whereNotExists(function($query)
            {
                $query->select(DB::raw(1))
                    ->from('stocks')
                    ->whereRaw('orders.product_id = stocks.product_id')
                    ->whereRaw('orders.batch = stocks.batch');
            })
            ->get();
        $rem_pcost = DB::select("SELECT SUM(a.rcost) as remaining_pcost FROM (SELECT ((stocks.units - stocks.spoilt - coalesce(SUM(sales.units),0)) * ((stocks.pcost+stocks.ccost+stocks.tcost)/stocks.units)) as rcost FROM stocks LEFT JOIN sales ON stocks.id = sales.stock_id WHERE stocks.soldout = 0 GROUP BY stocks.id ORDER BY stocks.id) as a");
        $spoilt_pcost = DB::select("SELECT SUM(a.spoilt_cost) as spoilt_pcost FROM (SELECT stocks.id, stocks.units, stocks.spoilt, (stocks.pcost+stocks.ccost+stocks.tcost)/stocks.units as rcost, (stocks.spoilt*((stocks.pcost+stocks.ccost+stocks.tcost)/stocks.units)) as spoilt_cost FROM stocks WHERE stocks.spoilt > 0) as a");
        $balance = collect($rem_pcost)->sum('remaining_pcost');
        $spoilt = collect($spoilt_pcost)->sum('spoilt_pcost')/2;

        $laptop_date = '2022-12-21';
        //$laptop_age_years = Carbon::parse($laptop_date)->age;
        $laptop_age_months = Carbon::parse($laptop_date)->diffInMonths(Carbon::now());
        $laptop_current_value = 20000*((100 - (2*$laptop_age_months))/100);
        $laptop = $laptop_current_value;

        $cash = Cash::orderBy('id', 'desc')->first()->amount;

        $total = $shipment[0]->total_product_cost + $balance + $spoilt + $laptop + $cash;

        return $total;
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

    public function get_partners(){
        $data = Partner::all()->sortBy('id')->values();
        return datatables()->of($data)
            ->addColumn('added_by', function ($data) {
                return isset($data->user->username) ? $data->user->username : '';
            })
            ->addColumn('date_added', function ($data) {
                return isset($data->created_at) ? $data->created_at : '';
            })
            ->addColumn('profit_share_percentage', function ($data) {
                $profit_share = $data->profit_share + 0;
                return $profit_share."%";
            })
            ->addColumn('capital_share', function ($data) {
                $total = $this->getCapitalShare();
                $share = $total * $data->profit_share/100;
                return 'KES. ' . number_format($share, 0, ',', ',');
            })
            ->addColumn('partner_status', function ($data) {
                $stat = $data->status_id;
                return $stat === 1 ? "Active" : "Inactive";
            })
            ->addIndexColumn()
            ->make(true);
    }

    public function get_shops(){
        $data = Shop::all()->sortBy('id')->values();
        return datatables()->of($data)
            ->addColumn('date_added', function ($data) {
                return isset($data->created_at) ? $data->created_at : '';
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

    public function add_partner(Request $req) {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'name' => 'required|unique:products|max:50',
            'email' => 'required|max:50',
            'phone' => 'required|max:50',
            'profit_share_percentage' => 'required',
            'status_id' => 'required',
        ]);

        $data_input = $req->all();
        if ($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }
        $data_input['profit_share'] = $req->profit_share_percentage;
        $data_input['user_id'] = auth()->user()->id;

        $partner = Partner::updateOrCreate(['id' => $id], $data_input);

        $message = array();
        if ($partner) {
            $message['message'] = 'Data saved successfully';

            return response()->json($message)->setStatusCode(200);
        } else {

            $message['message'] = 'Data failed to save';

            return response()->json($message)->setStatusCode(400);
        }
    }

    public function add_shop(Request $req) {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'name' => 'required|unique:products|max:50',
            'location' => 'required',
        ]);

        $data_input = $req->all();
        if ($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }

        $shop = Shop::updateOrCreate(['id' => $id], $data_input);

        $message = array();
        if ($shop) {
            $message['message'] = 'Data saved successfully';

            return response()->json($message)->setStatusCode(200);
        } else {

            $message['message'] = 'Data failed to save';

            return response()->json($message)->setStatusCode(400);
        }
    }

}
