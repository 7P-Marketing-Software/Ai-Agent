<?php

use Illuminate\Support\Facades\Route;
use Modules\Question\Http\Controllers\QuestionController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth:sanctum'])->prefix('questions')->group(function () {
    Route::get('/', [QuestionController::class, 'index']); 
    Route::get('/{id}', [QuestionController::class, 'show']);  
    Route::post('/', [QuestionController::class, 'store'])->middleware([AdminMiddleware::class]);
    Route::post('/{id}/update', [QuestionController::class, 'update'])->middleware([AdminMiddleware::class]);
    Route::delete('/{id}', [QuestionController::class, 'destroy'])->middleware([AdminMiddleware::class]);     
});
