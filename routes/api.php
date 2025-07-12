<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Device\DeviceCategotyController;
use App\Http\Controllers\Device\DeviceDescriptionController;
use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Device\DeliverController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\User\UserDeviceController;
use App\Http\Controllers\Sms\SmsController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Blog\BlogCategoryController;
use App\Http\Controllers\Blog\AuthorController;
use App\Http\Controllers\Blog\BlogController;

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
    Route::post('auth',[UserController::class,'Auth']);
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

Route::apiResource('technician-request',RequestController::class)
    ->middleware('check.role:1,2');
Route::get('user-device',[UserDeviceController::class,'customerDevice'])
    ->middleware('auth:sanctum');
Route::get('technician-device',[UserDeviceController::class,'technicianDevice'])
    ->middleware('check.role:1,2');

Route::apiResource('sms',SmsController::class)
    ->middleware('check.role:1');
Route::post('send-sms',[SmsController::class,'sendSms'])
    ->middleware('check.role:1');

Route::get('status',[StatusController::class,'index']);
Route::put('status',[StatusController::class,'update'])
    ->middleware('check.role:1');

Route::prefix('blog')->group(function (){
    Route::get('index-category',[BlogCategoryController::class,'index']);
    Route::get('show-category/{category}',[BlogCategoryController::class,'show']);
    Route::apiResource('category',BlogCategoryController::class)
        ->middleware('check.role:1,4');

    Route::apiResource('author',AuthorController::class)
    ->middleware('check.role:1,4');
    Route::get('index-author',[AuthorController::class,'index']);
    Route::get('show-author/{author}',[AuthorController::class,'show']);
});
Route::apiResource('blog',BlogController::class)->middleware('check.rol:1,4');
Route::get('index-blog',[BlogController::class,'index']);
Route::post('show-blog/{blog}',[BlogController::class,'show']);
