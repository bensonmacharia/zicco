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
    Route::get('order/manage', [App\Http\Controllers\OrderController::class, 'index'])->name('order');
    Route::post('admin/order/save', [App\Http\Controllers\OrderController::class, 'store']);
    Route::post('admin/order/payment', [App\Http\Controllers\OrderController::class, 'payment']);
    Route::get('admin/order/get-data', [App\Http\Controllers\OrderController::class, 'getData']);
    Route::get('admin/order/get-order/{id}', [App\Http\Controllers\OrderController::class, 'getOrder']);
    Route::get('admin/order/get-contributions/{id}', [App\Http\Controllers\OrderController::class, 'getContributions']);
    Route::get('admin/order/get-transfers/{id}', [App\Http\Controllers\OrderController::class, 'getTransfers']);
    Route::get('stock/summary', [App\Http\Controllers\StockController::class, 'summary'])->name('summary');
    Route::get('stock/aggregate', [App\Http\Controllers\StockController::class, 'aggregate'])->name('aggregate');
    Route::get('stock/balances', [App\Http\Controllers\StockController::class, 'balances'])->name('balances');
    Route::get('stock/soldout', [App\Http\Controllers\StockController::class, 'soldout'])->name('soldout');
    Route::get('stock/load_stock_summary', [App\Http\Controllers\StockController::class, 'load_stock_summary']);
    Route::get('stock/load_stock_aggregate', [App\Http\Controllers\StockController::class, 'load_stock_aggregate']);
    Route::get('stock/load_stock_balances', [App\Http\Controllers\StockController::class, 'load_stock_balances']);
    Route::get('stock/manage', [App\Http\Controllers\StockController::class, 'index'])->name('stock');
    Route::get('admin/stock/get-data', [App\Http\Controllers\StockController::class, 'getData']);
    Route::get('admin/stock/get-soldout', [App\Http\Controllers\StockController::class, 'getSoldOut']);
    Route::get('admin/stock/get-stock-transfers/{id}', [App\Http\Controllers\StockController::class, 'getStockTransfers']);
    Route::post('admin/stock/save', [App\Http\Controllers\StockController::class, 'store']);
    Route::get('admin/category/get-data', [App\Http\Controllers\CategoryController::class, 'getData']);
    Route::post('admin/category/save', [App\Http\Controllers\CategoryController::class, 'store']);
    Route::post('admin/category/destroy/{id}', [App\Http\Controllers\CategoryController::class, 'destroy']);
    Route::get('admin/customer', [App\Http\Controllers\AdminController::class, 'index'])->name('customer');
    Route::get('admin/partners', [App\Http\Controllers\AdminController::class, 'partner'])->name('partners');
    Route::post('admin/partner/save', [App\Http\Controllers\AdminController::class, 'add_partner']);
    Route::get('admin/partner/get-data', [App\Http\Controllers\AdminController::class, 'get_partners']);
    Route::get('admin/guide', [App\Http\Controllers\AdminController::class, 'guide'])->name('guide');
    Route::get('admin/customer/get-data', [App\Http\Controllers\AdminController::class, 'getData']);
    Route::post('admin/customer/save', [App\Http\Controllers\AdminController::class, 'store']);
    Route::get('expense/manage', [App\Http\Controllers\ExpensesController::class, 'index'])->name('expense');
    Route::get('admin/expense/get-data', [App\Http\Controllers\ExpensesController::class, 'getData']);
    Route::post('admin/expense/save', [App\Http\Controllers\ExpensesController::class, 'store']);
    Route::get('sales/manage', [App\Http\Controllers\SalesController::class, 'index'])->name('sales');
    Route::get('admin/sale/update-stock-sales', [App\Http\Controllers\SalesController::class, 'updateStockSales']);
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
