<?php

use Illuminate\Support\Facades\Route;
use Modules\Answer\Http\Controllers\AnswerController;

Route::middleware(['auth:sanctum'])->prefix('answers')->group(function () {
    Route::get('/', [AnswerController::class, 'index']); 
    Route::post('/',[AnswerController::class,'store']);
    Route::get('/generate-report',[AnswerController::class,'generateReport']);
});
