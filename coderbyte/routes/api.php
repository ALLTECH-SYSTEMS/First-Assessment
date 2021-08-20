<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;
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




Route::prefix('auth')->group(function () {
    //for registration
    Route::post('/register', [AuthController::class, 'register']);
    //for login
    Route::post('/login', [AuthController::class, 'login']);
});


Route::group(['middleware' => ['auth:sanctum']], function () {
    //to get current logged in user
    Route::get('/me', function(Request $request) {
        return auth()->user();
    });
    //for logining out
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    
    Route::prefix('request')->group(function () {
        //get all request
        Route::get('/index', [RequestController::class, 'index']);
        //get all product owners request
        Route::get('/indexProductOwner/{id}', [RequestController::class, 'indexProductOwner']);
        //get all photographer request
        Route::get('/indexPhotographer/{id}', [RequestController::class, 'indexPhotographer']);
        //create new request by product owner
        Route::post('/create', [RequestController::class, 'create']);
        //assigning request to a photographer by admin
        Route::post('/assign/{id}', [RequestController::class, 'assign']);
        //uploading images by photographer
        Route::post('/upload/{id}', [RequestController::class, 'upload']);
        //approving a request image by product owner
        Route::get('/approve/{id}', [RequestController::class, 'approve']);
        //rejecting a request image by product owner
        Route::get('/reject/{id}', [RequestController::class, 'reject']);
        //deleting request by product owner
        Route::get('/delete/{id}', [RequestController::class, 'destroy']);
        //editing request by product owner
        Route::post('/edit-request/{id}', [RequestController::class, 'editRequest']);
        //editing photographer uploaded image by photographer
        Route::post('/edit-request-image/{id}', [RequestController::class, 'editImages']);
    });
});
