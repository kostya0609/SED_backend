<?php
use Illuminate\Support\Facades\Route;
use SED\Common\Middleware\CheckAccessToAdmin;
use SED\Report\Controllers\v1\ReportController;

Route::middleware(CheckAccessToAdmin::class)->prefix('/sed/report/v1')->group(function () {
	Route::prefix('reports')->group(function () {
		Route::post('generate-excel', ReportController::class . '@generateExcel');
		Route::post('/list', ReportController::class . '@getAll');
	});
});