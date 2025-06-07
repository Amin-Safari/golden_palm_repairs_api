<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\DescriptionStatisticsController;
use App\Http\Controllers\Auth\SmsVerificationController;
use App\Http\Controllers\DeviceCategoryController;
use App\Http\Controllers\DeviceDescriptionController;
use App\Http\Controllers\DeviceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/send-code', [AuthController::class, 'sendVerificationCode']);
Route::post('/auth/verify', [AuthController::class, 'verifyCode']);

Route::prefix('auth')->group(function () {
    Route::post('send-verification-code', [SmsVerificationController::class, 'sendVerificationCode']);
    Route::post('verify-code', [SmsVerificationController::class, 'verifyCode']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // مشتریان
    Route::middleware('role:customer')->group(function () {
        Route::apiResource('repairs', RepairController::class)->only(['index', 'store', 'show']);
    });

    // پذیرش
    Route::middleware('role:reception')->group(function () {
        Route::apiResource('repairs', RepairController::class)->except(['destroy']);
        Route::post('/repairs/{repair}/assign', [RepairController::class, 'assignTechnician']);
    });

    // مالی
    Route::middleware('role:financial')->group(function () {
        Route::post('/repairs/{repair}/set-price', [RepairController::class, 'setPrice']);
    });

    // تعمیرکاران
    Route::middleware('role:technician')->group(function () {
        Route::apiResource('repairs', RepairController::class)->only(['index', 'show', 'update']);
        Route::post('/repairs/{repair}/update-status', [RepairController::class, 'updateStatus']);
    });

    // ارباب (مدیر)
    Route::middleware('role:owner')->group(function () {
        Route::apiResource('repairs', RepairController::class);
    });

    Route::get('/statistics/monthly', [DescriptionStatisticsController::class, 'monthlyStatistics']);
    Route::get('/statistics/yearly', [DescriptionStatisticsController::class, 'yearlyStatistics']);
});

Route::prefix('device-categories')->group(function () {
    Route::get('/', [DeviceCategoryController::class, 'index']);
    Route::post('/', [DeviceCategoryController::class, 'store']);
    Route::put('/{deviceCategory}', [DeviceCategoryController::class, 'update']);
    Route::delete('/{deviceCategory}', [DeviceCategoryController::class, 'destroy']);
});

Route::prefix('device-descriptions')->group(function () {
    Route::get('/', [DeviceDescriptionController::class, 'index']);
    Route::post('/', [DeviceDescriptionController::class, 'store']);
    Route::put('/{deviceDescription}', [DeviceDescriptionController::class, 'update']);
    Route::delete('/{deviceDescription}', [DeviceDescriptionController::class, 'destroy']);
});

Route::prefix('devices')->group(function () {
    Route::get('/', [DeviceController::class, 'index']);
    Route::post('/', [DeviceController::class, 'store']);
    Route::put('/{device}', [DeviceController::class, 'update']);
    Route::delete('/{device}', [DeviceController::class, 'destroy']);
    Route::get('/get-user-name', [DeviceController::class, 'getUserName']);
    Route::post('/{device}/mark-as-ready', [DeviceController::class, 'markAsReady']);
});

Route::prefix('repairs')->group(function () {
    Route::get('/', [RepairController::class, 'index']);
    Route::post('/', [RepairController::class, 'store']);
    Route::put('/{repair}', [RepairController::class, 'update']);
    Route::delete('/{repair}', [RepairController::class, 'destroy']);
    Route::post('/{repair}/verify-code', [RepairController::class, 'verifyCode']);
});
