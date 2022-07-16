<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//API route for register new user
//Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);
//API route for login user
//Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

//Protecting Routes
/*
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/profile', function(Request $request) {
        return auth()->user();
    });

    // API route for logout user
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);

    // product
    Route::get('product/get-data', [App\Http\Controllers\API\ProductController::class, 'getData']);
    Route::post('product/save', [App\Http\Controllers\API\ProductController::class, 'store']);
    Route::post('product/destroy/{id}', [App\Http\Controllers\API\ProductController::class, 'destroy']);

    // category
    Route::get('category/get-data', [App\Http\Controllers\API\CategoryController::class, 'getData']);
    Route::post('category/save', [App\Http\Controllers\API\CategoryController::class, 'store']);
    Route::post('category/destroy/{id}', [App\Http\Controllers\API\CategoryController::class, 'destroy']);
});
*/
