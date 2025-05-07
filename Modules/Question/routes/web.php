<?php

use Illuminate\Support\Facades\Route;
use Modules\Question\Http\Controllers\QuestionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('questions', QuestionController::class)->names('question');
});
