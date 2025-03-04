<?php
use Illuminate\Support\Facades\Route;
use SED\Documents\Common\Controllers\v1\{
	DocumentThemeController,
	DocumentTypeController,
	DocumentController,
	BasedCreationController
};

Route::prefix('/sed/documents/v1')->group(function () {

	Route::prefix('/types')->group(function () {
		Route::post('/list', DocumentTypeController::class . '@getAll');
	});

	Route::prefix('documents')->group(function () {
		Route::post('/list', DocumentController::class . '@getAll');
		Route::post('/list-v2', DocumentController::class . '@getAllV2');
		Route::post('/need-action-list', DocumentController::class . '@getNeedActions');
		Route::post('/need-action-list-v2', DocumentController::class . '@getNeedActionsV2');
		Route::post('/need-action-count', DocumentController::class . '@getNeedActionCount');
		Route::post('/need-action-subuser-list', DocumentController::class . '@getNeedActionSubusers');
		Route::post('/need-action-subuser-list-v2', DocumentController::class . '@getNeedActionSubusersV2');
		Route::post('/need-action-subuser-count', DocumentController::class . '@getNeedActionSubuserCount');
		Route::post('/search-by-number', DocumentController::class . '@searchByNumber');
		Route::post('/search-by-theme', DocumentController::class . '@searchByTheme');
		Route::post('/get-all-statuses', DocumentController::class . '@getAllStatuses');
	});

	Route::prefix('/themes')->group(function () {
		Route::post('/list', DocumentThemeController::class . '@getAll');
	});

	Route::prefix('/based-creation')->group(function () {
		Route::post('/create-from', BasedCreationController::class . '@createFrom');
	});
});