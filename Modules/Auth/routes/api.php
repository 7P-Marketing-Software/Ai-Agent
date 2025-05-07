<?php

use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\AuthController;
use Modules\Auth\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CheckBanMiddleware;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware(['auth:sanctum', CheckBanMiddleware::class]);
    Route::post('forget-password', [AuthController::class, 'forgetPassword']);
    Route::post('check-phone-otp-forget-password', [AuthController::class, 'checkPhoneOTPForgetPassword']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
});

Route::prefix('admin')->middleware(AdminMiddleware::class)->group(function () {
    Route::delete('destroy-user/{id}', [AdminController::class, 'destroyUser']);
    Route::get('get-all-users', [AdminController::class, 'getAllUsers']);
    Route::post('ban-user', [AdminController::class, 'banUser']);
    Route::post('remove-ban/{id}', [AdminController::class, 'removeBan']);
});
