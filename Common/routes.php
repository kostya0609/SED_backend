<?php
use Illuminate\Support\Facades\Route;
use SED\Common\Controllers\v1\InitController;
use SED\Common\Controllers\v1\SubuserController;

Route::prefix('/sed/v1')->group(function () {
	Route::post('/init', InitController::class . '@getInitialData');
	Route::post('/get-all-subusers', SubuserController::class . '@getAllSubusers');	
});