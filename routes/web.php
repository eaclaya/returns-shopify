<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppController;
use App\Http\Controllers\RequestController;
use App\Http\Middleware\AccountCode;
use App\Http\Middleware\CustomerToken;
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

Route::get('/', [AppController::class, 'start'])->name('orders.guest');
Route::group(['prefix' => '/{code?}', 'middleware' => AccountCode::class], function(){
    Route::get('/', [AppController::class, 'showOrderSearch'])->name('orders.search');
    Route::post('/customer/token', [AppController::class, 'setCustomerToken'])->name('customers.token');
    Route::get('/customer/token', [AppController::class, 'removeCustomerToken'])->name('customers.token');
    Route::get('/orders/token/{token}', [AppController::class, 'getOrderByToken'])->name('orders.token');
    Route::middleware([CustomerToken::class])->group(function(){
        Route::get('/orders/review', [AppController::class, 'showOrderReview'])->name('orders.review');
        Route::get('/orders/details', [AppController::class, 'showOrderDetails'])->name('orders.details');
        Route::post('/orders/get', [AppController::class, 'getOrder'])->name('app_orders.get');

        Route::get('/requests/details', [RequestController::class, 'showRequestDetails'])->name('requests.details');
        Route::get('/requests/success', [RequestController::class, 'showRequestSuccess'])->name('requests.success');
        Route::post('/requests/save', [RequestController::class, 'saveRequest'])->name('requests.save');
        Route::get('/requests/review', [RequestController::class, 'showRequestReview'])->name('requests.review');
        Route::get('/requests/step/{id}', [RequestController::class, 'showRequestStep'])->name('requests.step');

        Route::post('products/get', [AppController::class, 'getProducts'])->name('app_products.get');
        Route::post('products/filter', [AppController::class, 'filterProducts'])->name('app_products.filter');
    });
});


require __DIR__.'/auth.php';
