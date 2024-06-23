<?php
use Illuminate\Support\Facades\Route;
use SED\Documents\Common\Controllers\v1\{
	DocumentThemeController,
	DocumentTypeController,
	DocumentController
};

Route::prefix('/sed/documents/v1')->group(function () {

	Route::prefix('/types')->group(function () {
		Route::post('/list', DocumentTypeController::class . '@getAll');
	});

	Route::prefix('documents')->group(function () {
		Route::post('/list', DocumentController::class . '@getAll');
		Route::post('/list-v2', DocumentController::class . '@getAllV2');
		Route::post('/need-action-list', DocumentController::class . '@getNeedActions');
		Route::post('/need-action-count', DocumentController::class . '@getNeedActionCount');
	});

	Route::prefix('/themes')->group(function () {
		Route::post('/list', DocumentThemeController::class . '@getAll');
	});

});