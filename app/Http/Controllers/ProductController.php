<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use File;

class ProductController extends Controller {

    public function index() {
        $category = Category::all()->sortBy('name')->values();
        return view('pages/product/index', compact('category'));
    }

    public function getData() {
        $data = Product::all()->sortByDesc('created_at')->values();

        return datatables()->of($data)
                        ->addColumn('category_name', function ($data) {
                            return isset($data->category->name) ? $data->category->name : '';
                        })
                        ->addColumn('raw_price', function ($data) {
                            return $data->price ? 'KES. ' . number_format($data->price, 0, ',', '.') : '';
                        })
                        ->addIndexColumn()
                        ->make(true);
    }

    public function store(Request $req) {
        $id = $req->id ?: 0;

        $validated = $req->validate([
            'name' => 'required|unique:products|max:50',
            'description' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'image' => 'required',
        ]);

        $data_input = $req->all();
        if ($id) {
            $data_input['updated_at'] = date('Y-m-d H:i:s');
        } else {
            $data_input['created_at'] = date('Y-m-d H:i:s');
        }

        $data_input['price'] = str_replace('.', '', $data_input['price']);
        $data_input['user_id'] = auth()->user()->id;

        $name_lama = '';
        if ($req->hasFile('image')) {
            if ($id) {
                $product = Product::find($id);
                $name_lama = $product->image;
            }

            if ($name_lama != $req->file('image')->getClientOriginalName() && file_exists(public_path('temp_product/avatar.jpg'))) {
                $filename = $req->image->getClientOriginalName(); // getting file extension
                $filename = preg_replace('/\s/', '-', $filename);

                $name_baru = time() . "_" . $filename;
                $data_input['image'] = $name_baru;
                File::move(public_path('temp_product/avatar.jpg'), public_path('image_product/' . $name_baru));
            } else {
                unset($data_input['image']);
            }
        }

        $product = Product::updateOrCreate(['id' => $id], $data_input);

        if ($product) {
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
        $product = Product::where('id', $id)->first();

        if ($product->delete()) {
            $message = array();
            $message['message'] = 'Data deleted successfully';

            return response()->json($message)->setStatusCode(200);
        } else {

            $message = array();
            $message['message'] = 'Data failed to delete';

            return response()->json($message)->setStatusCode(400);
        }
    }

    public function upload_product(Request $request) {
        $file = new Filesystem;
        $file->cleanDirectory(public_path('temp_product'));
        if ($request->hasFile('avatar')) {

            $filename = $request->avatar->getClientOriginalName(); // getting file extension
            $filename = preg_replace('/\s/', '-', $filename);

            $destinationPath = 'temp_product'; // upload path
            $extension = $request->avatar->getClientOriginalExtension(); // getting file extension

            $name_baru = $filename;

            $request->avatar->move(public_path("$destinationPath"), $name_baru);
        }
    }

    public function catalog() {
        $product = Product::with('category')->latest()->
                        where('user_id', auth()->user()->id)->get();

        return view('pages/catalog/index', compact('product'));
    }

}
