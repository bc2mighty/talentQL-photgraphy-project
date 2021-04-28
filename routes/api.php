<?php

use App\Http\Controllers\PhotographerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductOwnerController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Photographer Routes
Route::group(['prefix' => 'photographer'], function (){
    Route::post('/create', [PhotographerController::class, 'store']);
    Route::put('/{photographer}', [PhotographerController::class, 'update']);
    Route::post('/{photographer}/product/{product}', [PhotographerController::class, 'pictures']);
    Route::post('/login', [PhotographerController::class, 'login']);
});

// Prouct Owners' Routes
Route::group(['prefix' => 'productOwner'], function (){
    Route::post('/create', [ProductOwnerController::class, 'store']);
    Route::put('/{productOwner}', [ProductOwnerController::class, 'update']);
    Route::post('/login', [ProductOwnerController::class, 'login']);
    Route::put('/{productOwner}/{product}', [ProductOwnerController::class, 'product']);
    Route::get('/{productOwner}/products', [ProductOwnerController::class, 'products']);
    Route::get('/{productOwner}/products/photographs/unapproved', [ProductOwnerController::class, 'unapproved']);
    Route::get('/{productOwner}/products/photographs/approved', [ProductOwnerController::class, 'approved']);
    Route::post('/{productOwner}/productPhotograph/{productPhotograph}/approve', 
        [ProductOwnerController::class, 'approve'
    ]);
});

// Prouct Routes
Route::resource('products', ProductController::class);