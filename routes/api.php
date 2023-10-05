<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group( function () {
    Route::get('/productslist', [ProductController::class,'productslist']);
    Route::get('/createorder', [OrderController::class,'createOrder']);
    Route::post('/addtocart', [OrderController::class,'addtoCart']);
    Route::post('/showwishlist', [OrderController::class,'showWishlist']);
    Route::post('/updatecartitems', [OrderController::class,'updateCartItems']);
    Route::delete('/cartitemdelete', [OrderController::class,'deleteCartitem']);

    //make order payments
    Route::post('/makepaymentfororder', [PaymentController::class,'makePayment']);
    Route::post('/confirmorderpayment', [PaymentController::class,'confirmorderPaymentIntend']);

    
    Route::get('/showplacedrorder', [PaymentController::class,'showplacedorder']);

});

Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'createUser')->name('createUser');
    Route::any('login', 'loginUser')->name('login');
});
