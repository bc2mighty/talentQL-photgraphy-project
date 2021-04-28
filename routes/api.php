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
    // Account Creation
    Route::post('/create', [PhotographerController::class, 'store']);
    // Account Update
    Route::put('/{photographer}', [PhotographerController::class, 'update']);
    // Post Pictures taken for products in processing facilities
    Route::post('/{photographer}/product/{product}', [PhotographerController::class, 'pictures']);
    // Account Login
    Route::post('/login', [PhotographerController::class, 'login']);
});

// Prouct Owners' Routes
Route::group(['prefix' => 'productOwner'], function (){
    // Account Creation
    Route::post('/create', [ProductOwnerController::class, 'store']);
    // Account Update
    Route::put('/{productOwner}', [ProductOwnerController::class, 'update']);
    // Account Login
    Route::post('/login', [ProductOwnerController::class, 'login']);
    // Send Product To Processing Facility
    Route::put('/{productOwner}/{product}', [ProductOwnerController::class, 'product']);
    // Get Created Products
    Route::get('/{productOwner}/products', [ProductOwnerController::class, 'products']);
    // Get Thumbnails of Product Photographs that are not yet approved
    Route::get('/{productOwner}/products/photographs/unapproved', [ProductOwnerController::class, 'unapproved']);
    // Get Thumbnails and high Resolution Photographs of Products that were approved
    Route::get('/{productOwner}/products/photographs/approved', [ProductOwnerController::class, 'approved']);
    // Approve/Disapprove Product Photographs
    Route::post('/{productOwner}/productPhotograph/{productPhotograph}/approve', [ProductOwnerController::class, 'approve']);
});

// Prouct Routes CRUD
Route::resource('products', ProductController::class);