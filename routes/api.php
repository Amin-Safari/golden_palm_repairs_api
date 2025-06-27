<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Device\DeviceCategotyController;
use App\Http\Controllers\Device\DeviceDescriptionController;
use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Device\DeliverController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('device')->group(function (){
    Route::apiResource('category',DeviceCategotyController::class);
    Route::apiResource('description',DeviceDescriptionController::class);
    Route::apiResource('repair',DeviceController::class);
    Route::post('search_device',[DeviceController::class,'search_device']);
    Route::post('search_c_device',[DeviceController::class,'search_cdevice']);
    Route::post('search_date',[DeviceController::class,'search_date']);

});
Route::prefix('user')->group(function (){
    Route::post('check',[UserController::class,'check']);
    Route::post('Auth',[UserController::class,'Auth']);
    Route::post('login',[UserController::class,'login']);
    Route::post('create_user',[UserController::class,'createUser']);
    Route::post('search_user',[UserController::class,'searchUser']);
    Route::post('logout',[UserController::class,'logout']);
});
Route::prefix('delivery')->group(function (){
    Route::post('v_code',[DeliverController::class,'v_code']);
    Route::post('check_code',[DeliverController::class,'check_code']);
});
