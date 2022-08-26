<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/home/user-guide', function () {
    return view('guide');
});

Auth::routes();
//Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth']], function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('home/get_stock_summaries', [App\Http\Controllers\HomeController::class, 'get_stock_summaries']);
    Route::get('product/manage', [App\Http\Controllers\ProductController::class, 'index'])->name('product');
    Route::get('product/catalog', [App\Http\Controllers\ProductController::class, 'catalog'])->name('catalog');
    Route::get('admin/product/get-data', [App\Http\Controllers\ProductController::class, 'getData']);
    Route::post('admin/product/save', [App\Http\Controllers\ProductController::class, 'store']);
    Route::post('admin/product/destroy/{id}', [App\Http\Controllers\ProductController::class, 'destroy']);
    Route::post('admin/upload-product', [App\Http\Controllers\ProductController::class, 'upload_product']);
    Route::get('product/category', [App\Http\Controllers\CategoryController::class, 'index'])->name('category');
    Route::get('stock/summary', [App\Http\Controllers\StockController::class, 'summary'])->name('summary');
    Route::get('stock/load_stock_summary', [App\Http\Controllers\StockController::class, 'load_stock_summary']);
    Route::get('stock/manage', [App\Http\Controllers\StockController::class, 'index'])->name('stock');
    Route::get('admin/stock/get-data', [App\Http\Controllers\StockController::class, 'getData']);
    Route::post('admin/stock/save', [App\Http\Controllers\StockController::class, 'store']);
    Route::get('admin/category/get-data', [App\Http\Controllers\CategoryController::class, 'getData']);
    Route::post('admin/category/save', [App\Http\Controllers\CategoryController::class, 'store']);
    Route::post('admin/category/destroy/{id}', [App\Http\Controllers\CategoryController::class, 'destroy']);
    Route::get('admin/customer', [App\Http\Controllers\AdminController::class, 'index'])->name('customer');
    Route::get('admin/guide', [App\Http\Controllers\AdminController::class, 'guide'])->name('guide');
    Route::get('admin/customer/get-data', [App\Http\Controllers\AdminController::class, 'getData']);
    Route::post('admin/customer/save', [App\Http\Controllers\AdminController::class, 'store']);
    Route::get('sales/manage', [App\Http\Controllers\SalesController::class, 'index'])->name('sales');
    Route::post('admin/sale/save', [App\Http\Controllers\SalesController::class, 'store']);
    Route::get('admin/sale/get-data', [App\Http\Controllers\SalesController::class, 'getData']);
    Route::post('admin/sale/destroy/{id}', [App\Http\Controllers\SalesController::class, 'destroy']);
    Route::get('report/sales/by-date', [App\Http\Controllers\ReportsController::class, 'index'])->name('turnover');
    Route::get('report/sales/by-customer', [App\Http\Controllers\ReportsController::class, 'customer_sales'])->name('customer-sales');
    Route::get('report/sales/debtors', [App\Http\Controllers\ReportsController::class, 'debtors'])->name('debtors');
    Route::get('report/sales/get-debtors', [App\Http\Controllers\ReportsController::class, 'getDebtors']);
    Route::get('report/sales/today', [App\Http\Controllers\ReportsController::class, 'today'])->name('today');
    Route::get('report/sales/salesByDate/{start}/{end}', [App\Http\Controllers\ReportsController::class, 'salesByDate']);
    Route::get('report/sales/salesByCustomer/{customer}', [App\Http\Controllers\ReportsController::class, 'salesByCustomer']);
});
