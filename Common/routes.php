<?php
use Illuminate\Support\Facades\Route;
use SED\Common\Controllers\v1\InitController;

Route::prefix('/sed/v1')->group(function () {
	Route::post('/init', InitController::class . '@getInitialData');
});