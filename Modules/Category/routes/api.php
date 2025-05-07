<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware(['auth:sanctum'])->prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']); 
    Route::get('/{id}', [CategoryController::class, 'show']);  
    Route::post('/', [CategoryController::class, 'store'])->middleware([AdminMiddleware::class]);
    Route::post('/{id}/update', [CategoryController::class, 'update'])->middleware([AdminMiddleware::class]);
    Route::delete('/{id}', [CategoryController::class, 'destroy'])->middleware([AdminMiddleware::class]);      
    Route::post('/{id}/make-root', [CategoryController::class, 'makeRoot'])->middleware([AdminMiddleware::class]); 
});