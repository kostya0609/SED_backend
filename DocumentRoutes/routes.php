<?php

use Illuminate\Support\Facades\Route;
use SED\Common\Middleware\CheckAccessToAdmin;
use SED\DocumentRoutes\Features\DocumentTemplates\Controllers\v1\DocumentTemplatesController;
use SED\DocumentRoutes\Features\Partitions\Controllers\v1\PartitionController;
use SED\DocumentRoutes\Features\Routes\Controllers\v1\RouteController;

Route::middleware(CheckAccessToAdmin::class)->prefix('sed/document-routes/v1')->group(function () {

	Route::prefix('/routes')->group(function () {
		Route::post('/create', RouteController::class . '@create');
		Route::post('/edit', RouteController::class . '@edit');
		Route::post('/delete', RouteController::class . '@delete');
		Route::post('/list', RouteController::class . '@list');
		Route::post('/get', RouteController::class . '@get');
		Route::post('/get-additional-data', RouteController::class . '@getAdditionalData');
		Route::post('/deactivate', RouteController::class . '@deactivate');
	});

	Route::prefix('/document-templates')->group(function () {
		Route::post('/create', DocumentTemplatesController::class . '@create');
		Route::post('/edit', DocumentTemplatesController::class . '@edit');
		Route::post('/delete', DocumentTemplatesController::class . '@delete');
		Route::post('/deactivate', DocumentTemplatesController::class . '@deactivate');
		Route::post('/list', DocumentTemplatesController::class . '@list');
		Route::post('/get', DocumentTemplatesController::class . '@get');
	});

	Route::prefix('/partitions')->group(function () {
		Route::post('/create', PartitionController::class . '@create');
		Route::post('/edit', PartitionController::class . '@edit');
		Route::post('/delete', PartitionController::class . '@delete');
		Route::post('/get-tree', PartitionController::class . '@getTree');
		Route::post('/get', PartitionController::class . '@get');
	});

});