<?php

use Illuminate\Support\Facades\Route;
use Modules\Answer\Http\Controllers\AnswerController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('answers', AnswerController::class)->names('answer');
});
