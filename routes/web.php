<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/', function(){ return redirect('/channel/passenger');});
Route::get('/home', function(){ return redirect('/channel/passenger');});


//main channel
Route::get('/channel/passenger', 'HomeController@listPassenger');
Route::get('/channel/driver', 'HomeController@listDriver');
Route::get('/channel/trip', 'HomeController@listTrip');
Route::get('/channel/trip/{userid}', 'HomeController@listTripByPassengerUser');
Route::get('/channel/tripByDriver/{userid}', 'HomeController@listTripByDriverUser');
Route::get('/channel/vehicle', 'HomeController@listVehicle');
Route::get('/channel/driverRegister', 'HomeController@driverRegister');
Route::get('/channel/driverApplyHistory', 'HomeController@driverApplyHistory');


//ajax req
Route::get('/ajax/getDriverDetail/{userid}', 'UserController@ajaxGetDriverDetail');
Route::get('/ajax/getVehicleDetailByUser/{userid}', 'UserController@ajaxGetVehicleDetailByUser');
Route::get('/ajax/getApplyDetail/{registrationId}', 'UserController@ajaxGetRegistrationDetail');
Route::get('/ajax/driverRegisterOperate', 'HomeController@driverRegisterOperate');
Route::get('/ajax/getTripDetail/{tripId}', 'HomeController@ajaxGetTripDetail');


