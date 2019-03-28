<?php
use Illuminate\Support\Facades\Route;

Route::prefix("api")->group(function() {
	Route::middleware('web')->group(function() {
		Route::get('/', function() { return response("", 204); });
		Route::post('login', 'Auth\LoginController@login');
		Route::post('logout', 'Auth\LoginController@logout');
	});
	// TODO:: lock API routes to use web middleware
	Route::resource('bills', BillController::class);
});