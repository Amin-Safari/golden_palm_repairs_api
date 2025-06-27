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
    Route::apiResource('category',DeviceCategotyController::class)
        ->middleware('check.role:1,2');
    Route::apiResource('description',DeviceDescriptionController::class)
        ->middleware('check.role:1,2');
    Route::apiResource('repair',DeviceController::class)
        ->middleware('check.role:1,2');
    Route::post('search_device',[DeviceController::class,'search_device'])
        ->middleware('check.role:1,2');
    Route::post('search_c_device',[DeviceController::class,'search_cdevice'])
        ->middleware('check.role:1,2');
    Route::post('search_date',[DeviceController::class,'search_date'])
        ->middleware('check.role:1,2');

});
Route::prefix('user')->group(function (){
    Route::post('check',[UserController::class,'check'])->middleware('check.role:1');
    Route::post('Auth',[UserController::class,'Auth']);
    Route::post('login',[UserController::class,'login']);
    Route::post('create_user',[UserController::class,'createUser'])
        ->middleware('check.role:1');
    Route::post('search_user',[UserController::class,'searchUser'])
        ->middleware('check.role:1');
    Route::post('logout',[UserController::class,'logout'])
        ->middleware('auth:sanctum');
});
Route::prefix('delivery')->group(function (){
    Route::post('v_code',[DeliverController::class,'v_code'])
        ->middleware('check.role:1');
    Route::post('check_code',[DeliverController::class,'check_code'])
        ->middleware('check.role:1');
});
