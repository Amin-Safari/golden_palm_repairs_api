<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\DescriptionStatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/send-code', [AuthController::class, 'sendVerificationCode']);
Route::post('/auth/verify', [AuthController::class, 'verifyCode']);

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
