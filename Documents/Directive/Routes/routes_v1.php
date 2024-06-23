<?php
use Illuminate\Support\Facades\Route;
use SED\Documents\Directive\Controllers\v1\DirectiveController;

Route::prefix('/sed/documents/directive/v1')->group(function () {
	Route::prefix('directives')->group(function () {
		Route::post('/create', DirectiveController::class . '@create');
		Route::post('/get', DirectiveController::class . '@getById');
		Route::post('/update', DirectiveController::class . '@update');
		Route::post('/delete', DirectiveController::class . '@delete');
		Route::post('/cancel', DirectiveController::class . '@cancel');
		Route::post('/upload-files', DirectiveController::class . '@uploadFiles');
	});
});