<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyAppController;
use App\Http\Middleware\AccountCode;

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
Route::group(['prefix' => '/{code?}', 'middleware' => AccountCode::class], function(){
    Route::post('/v1/orders/get', [ShopifyAppController::class, 'getOrder'])->name('shopify_orders.get');
    Route::post('/v1/products/get', [ShopifyAppController::class, 'getProducts'])->name('shopify_products.get');
    Route::post('/v1/products/handle', [ShopifyAppController::class, 'getProductsByHandle'])->name('shopify_products.handle');
    Route::post('/v1/discounts/rule', [ShopifyAppController::class, 'getDiscountRule'])->name('shopify_discounts.rule');
});
