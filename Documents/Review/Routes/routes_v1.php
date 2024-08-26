<?php
use Illuminate\Support\Facades\Route;
use SED\Documents\Review\Controllers\v1\ReviewController;

Route::prefix('/sed/documents/review/v1')->group(function () {
    Route::prefix('reviews')->group(function () {
        Route::post('/create', ReviewController::class . '@create');
        Route::post('/get', ReviewController::class . '@getById');
        Route::post('/update', ReviewController::class . '@update');
        Route::post('/delete', ReviewController::class . '@delete');
        Route::post('/cancel', ReviewController::class . '@cancel');
        Route::post('/upload-files', ReviewController::class . '@uploadFiles');
        Route::post('/send-to-approval', ReviewController::class . '@sendToApproval');
    });
});
