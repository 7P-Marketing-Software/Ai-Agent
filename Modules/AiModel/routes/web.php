<?php

use Illuminate\Support\Facades\Route;
use Modules\AiModel\Http\Controllers\AiModelController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('aimodels', AiModelController::class)->names('aimodel');
});
