<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Device\DeviceCategoryController;
use App\Http\Controllers\Device\DeviceDescriptionController;
use App\Http\Controllers\Device\DeviceController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Device\DeliverController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\User\UserDeviceController;
use App\Http\Controllers\Sms\SmsController;
use App\Http\Controllers\StatusController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Financial\FinancialController;
use App\Http\Controllers\User\UserMessageController;

Route::prefix('device')->middleware(['auth:sanctum', 'role:1,2'])->group(function () {
    Route::apiResource('category', DeviceCategoryController::class);
    Route::apiResource('description', DeviceDescriptionController::class);
    Route::apiResource('repair', DeviceController::class);
    Route::post('search_device', [DeviceController::class, 'search_device']);
    Route::post('search_c_device', [DeviceController::class, 'search_cdevice']);
    Route::post('search_date', [DeviceController::class, 'search_date']);
    Route::get('count-fixed', [DeviceController::class, 'countFixed']);
    Route::get('count-delivered-today', [DeviceController::class, 'countDeliveredWData']);
    Route::get('count-fixing', [DeviceController::class, 'countFixing']);
    Route::get('fixed-devices', [DeviceController::class, 'fixedDevices']);
    Route::get('by-code/{v_code}',[DeviceController::class,'findByVerification']);
});

Route::prefix('financial')->middleware(['auth:sanctum', 'role:1'])->group(function () {
    Route::get('income-this-month', [FinancialController::class, 'incomeThisMonth'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::get('income-today', [FinancialController::class, 'incomeToday'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::get('income-this-year', [FinancialController::class, 'incomeThisYear'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::get('technician-income', [FinancialController::class, 'technicianIncome'])
        ->middleware(['auth:sanctum', 'role:2']);
});

Route::prefix('user')->group(function () {
    Route::apiResource('user', UserController::class)
        ->middleware(['auth:sanctum', 'role:1']);
    Route::post('check', [UserController::class, 'check'])->middleware(['auth:sanctum', 'role:1']);
    Route::post('auth', [UserController::class, 'Auth']);
    Route::post('login', [UserController::class, 'login']);
    Route::post('create_user', [UserController::class, 'createUser'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::post('search_user', [UserController::class, 'searchUser'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::post('logout', [UserController::class, 'logout'])
        ->middleware('auth:sanctum');
    Route::get('technician', [UserController::class, 'technician'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::get('Message', [UserMessageController::class, 'index'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::post('Message', [UserMessageController::class, 'store']);
});
Route::prefix('delivery')->group(function () {
    Route::post('v_code', [DeliverController::class, 'v_code'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::post('check_code', [DeliverController::class, 'check_code'])
        ->middleware(['auth:sanctum', 'role:1']);
    Route::post('verify',[DeliverController::class,'verifyCode']);
});

Route::apiResource('technician-request', RequestController::class)
    ->middleware(['auth:sanctum', 'role:1,2']);
Route::get('user-device', [UserDeviceController::class, 'customerDevice'])
    ->middleware('auth:sanctum');
Route::get('technician-device', [UserDeviceController::class, 'technicianDevice'])
    ->middleware(['auth:sanctum', 'role:1,2']);

Route::apiResource('sms', SmsController::class)
    ->middleware(['auth:sanctum', 'role:1']);
Route::post('send-sms', [SmsController::class, 'sendSms'])
    ->middleware(['auth:sanctum', 'role:1']);

Route::get('status', [StatusController::class, 'index']);
Route::put('status', [StatusController::class, 'update'])
    ->middleware(['auth:sanctum', 'role:1']);
