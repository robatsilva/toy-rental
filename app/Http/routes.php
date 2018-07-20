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



Route::get('/', 'HomeController@index');

Route::get('/login', 'RentalController@login');

Route::get('/toy/check/{id}', 'ToyController@check');

Route::auth();

Route::get('/cadastro', 'RegisterController@index');

Route::get('/kiosk', 'KioskController@index');
Route::post('/kiosk', 'KioskController@store');
Route::get('/kiosk/create', 'KioskController@create');
Route::get('/kiosk/list', 'KioskController@listKiosk');
Route::get('/kiosk/{id}', 'KioskController@edit');
Route::post('/kiosk/update/{id}', 'KioskController@update');
Route::get('/kiosk/toogle/{id}', 'KioskController@toogle');
Route::get('/kiosk/default/{id}', 'KioskController@setDefault');

Route::get('/customer/{kiosk_id}/{cpf}', 'CustomerController@show');

Route::get('/employe', 'EmployeController@index');
Route::post('/employe', 'EmployeController@store');
Route::get('/employe/create', 'EmployeController@create');
Route::get('/employe/{id}', 'EmployeController@edit');
Route::post('/employe/update/{id}', 'EmployeController@update');
Route::get('/employe/toogle/{id}', 'EmployeController@toogle');

Route::get('/toy', 'ToyController@index');
Route::post('/toy', 'ToyController@store');
Route::get('/toy/list', 'ToyController@listToys');
Route::get('/toy/create', 'ToyController@create');
Route::get('/toy/{id}', 'ToyController@edit');
Route::get('/toy/getByKioskId/{kiosk_id}', 'ToyController@getByKioskId');
Route::post('/toy/update/{id}', 'ToyController@update');
Route::get('/toy/toogle/{id}', 'ToyController@toogle');

Route::get('/sistema', 'RentalController@home');
Route::get('/rental', 'RentalController@index');
Route::post('/rental', 'RentalController@store');
Route::post('/rental/finish', 'RentalController@finish');
Route::get('/rental/{kiosk_id}', 'RentalController@index');
Route::post('/rental/extra-time', 'RentalController@extraTime');
Route::get('/rental/edit/{rental_id}', 'RentalController@edit');
Route::post('/rental/next-period/{rental_id}', 'RentalController@nextPeriod');
Route::post('/rental/pause/{rental_id}', 'RentalController@pause');
Route::post('/rental/cancel/{rental_id}', 'RentalController@cancel');
Route::post('/rental/calcule/{rental_id}', 'RentalController@calculeRental');

Route::get('/report', 'ReportController@rentals');
Route::post('/report', 'ReportController@reportByDate');
Route::get('/report/toys', 'ReportController@toys');
Route::post('/report/toys', 'ReportController@reportByToys');
Route::get('/report/cash', 'ReportController@cash');
Route::post('/report/cash', 'ReportController@reportByCash');
Route::get('/report/payment-way', 'ReportController@paymentWay');
Route::post('/report/payment-way', 'ReportController@reportByPaymentWay');

Route::post('/cash-flow', 'CashController@registerCashFlow');
Route::post('/cash', 'CashController@registerCash');
Route::get('/cash-flow/delete/{id}', 'CashController@deleteCashFlow');
Route::get('/cash/delete/{id}', 'CashController@deleteCash');

Route::get('/period', 'PeriodController@index');
Route::post('/period', 'PeriodController@store');
Route::get('/period/create', 'PeriodController@create');
Route::get('/period/list', 'PeriodController@listPeriods');
Route::get('/period/{id}', 'PeriodController@edit');
Route::get('/period/getByKioskId/{kiosk_id}', 'PeriodController@getByKioskId');
Route::post('/period/update/{id}', 'PeriodController@update');
Route::get('/period/toogle/{id}', 'PeriodController@toogle');

Route::get('/payment/session', 'PayController@getSession');
Route::post('/payment/pre-approvals', 'PayController@preApprovals');

