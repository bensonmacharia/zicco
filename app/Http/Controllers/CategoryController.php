<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return view('pages/category/index');
    }

    public function getData() {
        $data = Category::orderBy('name')->get();

        return datatables()->of($data)
        ->addIndexColumn()
        ->make(true);
    }


    public function store(Request $req){
        $id = $req->id?:0;

        if(!$id) {
            $validated = $req->validate([
                'name' => 'required|unique:categories|max:25',
            ]);
        }

        $data_input = $req->all();

        if($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }

        $category = Category::updateOrCreate(['id' => $id], $data_input);

        if ($category) {
			$message = array();
            $message['message'] = 'Data saved successfully';

            return response()->json($message)->setStatusCode(200);
		}else{

			$message = array();
            $message['message'] = 'Data failed to save';

            return response()->json($message)->setStatusCode(400);
		}
	}

	public function destroy($id){
        $category = Category::where('id', $id)->first();

        if ($category->delete()) {
			$message = array();
            $message['message'] = 'Data deleted successfully';

            return response()->json($message)->setStatusCode(200);
		}else{

			$message = array();
            $message['message'] = 'Data failed to delete';

            return response()->json($message)->setStatusCode(400);
		}
	}
}
