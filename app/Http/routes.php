<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'RentalController@home');

Route::auth();

Route::get('/cadastro', 'RegisterController@index');

Route::get('/kiosk', 'KioskController@index');
Route::post('/kiosk', 'KioskController@store');
Route::get('/kiosk/create', 'KioskController@create');
Route::get('/kiosk/list', 'KioskController@listKiosk');
Route::get('/kiosk/{id}', 'KioskController@edit');
Route::post('/kiosk/update/{id}', 'KioskController@update');
Route::get('/kiosk/remove/{id}', 'KioskController@destroy');

Route::get('/customer/{kiosk_id}/{cpf}', 'CustomerController@show');

Route::get('/toy', 'ToyController@index');
Route::post('/toy', 'ToyController@store');
Route::get('/toy/list', 'ToyController@listToys');
Route::get('/toy/create', 'ToyController@create');
Route::get('/toy/{kiosk_id}', 'ToyController@getByKioskId');

Route::get('/rental', 'RentalController@index');
Route::post('/rental', 'RentalController@store');
Route::get('/rental/home', 'RentalController@home');
Route::post('/rental/finish', 'RentalController@finish');
Route::get('/rental/{kiosk_id}', 'RentalController@index');
Route::post('/rental/extra-time', 'RentalController@extraTime');
Route::get('/rental/edit/{rental_id}', 'RentalController@edit');
Route::get('/rental/pause/{rental_id}', 'RentalController@pause');
Route::get('/rental/cancel/{rental_id}', 'RentalController@cancel');
Route::get('/rental/calcule/{rental_id}', 'RentalController@calculeRental');

Route::get('/report', 'ReportController@rentals');
Route::post('/report', 'ReportController@reportByDate');
Route::get('/report/toys', 'ReportController@toys');
Route::post('/report/toys', 'ReportController@reportByToys');
Route::get('/report/payment-way', 'ReportController@paymentWay');
Route::post('/report/payment-way', 'ReportController@reportByPaymentWay');

Route::get('/period', 'PeriodController@index');
Route::post('/period', 'PeriodController@store');
Route::get('/period/create', 'PeriodController@create');
Route::get('/period/list', 'PeriodController@listPeriods');
Route::get('/period/{kiosk_id}', 'PeriodController@getByKioskId');
