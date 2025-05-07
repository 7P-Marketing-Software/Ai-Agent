<?php

use Illuminate\Support\Facades\Route;
use Modules\AiModel\Http\Controllers\AiModelController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth:sanctum',AdminMiddleware::class])->prefix('ai-models')->group(function () {
    Route::get('/', [AiModelController::class, 'index']);
    Route::post('/', [AiModelController::class, 'store']);
    Route::post('/{id}/update', [AiModelController::class, 'update']);
    Route::delete('/{id}', [AiModelController::class, 'destroy']);
});
