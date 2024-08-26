<?php
use Illuminate\Support\Facades\Route;
use SED\Documents\ESZ\Controllers\v1\ESZController;

Route::prefix('/sed/documents/esz/v1')->group(function () {
	Route::prefix('esz')->group(function () {
		Route::post('/create', ESZController::class . '@create');
		Route::post('/get', ESZController::class . '@getById');
		Route::post('/update', ESZController::class . '@update');
		Route::post('/delete', ESZController::class . '@delete');
		Route::post('/upload-files', ESZController::class . '@uploadFiles');
		Route::post('/send-to-approval', ESZController::class . '@sendToApproval');
		Route::post('/cancellation', ESZController::class . '@cancellation');
	});
});
