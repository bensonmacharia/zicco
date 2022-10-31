<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public function index() {
        $expenses = Expense::all()->sortBy('name')->values();
        return view('pages/expense/index', compact('expenses'));
    }

    public function getData() {
        $data = Expense::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
            ->addColumn('expense_category', function ($data) {
                $cat = $data->expense_type;
                $typ = "";
                switch($cat) {
                    case(1):
                        $typ = "Shop Rent";
                        break;
                    case(2):
                        $typ = "Store Rent";
                        break;
                    case(3):
                        $typ = "Store Transfers";
                        break;
                    case(4):
                        $typ = "Customer Delivery";
                        break;
                    case(5):
                        $typ = "Receipt Books/Invoices";
                        break;
                    case(6):
                        $typ = "Business Permit";
                        break;
                    default:
                        $typ = "Others";
                }
                return $typ ?? '';
            })
            ->addColumn('expense_amount', function ($data) {
                return $data->amount ? 'KES. ' . number_format($data->amount, 0, ',', ',') : '';
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

    public function store(Request $req){
        $id = $req->id?:0;

        if(!$id) {
            $validated = $req->validate([
                'expense_type' => 'required',
                'description' => 'required',
                'amount' => 'required',
            ]);
        }

        $data_input = $req->all();

        if($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }

        $data_input['amount'] = str_replace('.', '', $data_input['amount']);
        $data_input['user_id'] = auth()->user()->id;

        $expense = Expense::updateOrCreate(['id' => $id], $data_input);

        if ($expense) {
            $message = array();
            $message['message'] = 'Data saved successfully';

            return response()->json($message)->setStatusCode(200);
        }else{

            $message = array();
            $message['message'] = 'Data failed to save';

            return response()->json($message)->setStatusCode(400);
        }
    }
}
