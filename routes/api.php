<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

//user info
Route::post('/passenger/getProfile', "Api\PassengerController@getProfile");
Route::post('/driver/getProfile', "Api\DriverController@getProfile");

//user trips
Route::post('/passenger/getTrip', "Api\PassengerController@getTrip");
Route::post('/driver/getTrip', "Api\DriverController@getTrip");


//register
Route::post('/passenger/register', "Api\UserController@passengerRegister");
//1 step driver register
Route::post('/driver/register', "Api\UserController@driverApply");
//2 steps driver register
Route::post('/driver/register/basicSubmit', "Api\UserController@driverBasicSubmit");
Route::post('/driver/register/moreDriverInfoSubmit', "Api\UserController@moreDriverInfoSubmit");
//3 steps
Route::post('/driver/register/drivingLicenseSubmit', "Api\UserController@drivingLicenseSubmit");
Route::post('/driver/register/vehicleSubmit', "Api\UserController@drivingVehicleSubmit");



//basic login logout
Route::post('/passenger/login', "Api\UserController@passengerLogin");
Route::post('/passenger/logout', "Api\PassengerController@passengerLogout");
Route::post('/driver/login', "Api\UserController@driverLogin");
Route::post('/driver/logout', "Api\DriverController@driverLogout");

//driver status
Route::post('/driver/goOffline', "Api\DriverController@goOffline");
Route::post('/driver/goOnline', "Api\DriverController@goOnline");

//update location
Route::post('/driver/updateLocation', "Api\DriverController@updateLocation");
Route::post('/passenger/updateLocationAndGetDriverList', "Api\PassengerController@updateLocationAndGetDriverList");

//trip passenger
Route::post('/passenger/requestOrder', "Api\PassengerController@requestOrder");
Route::post('/passenger/payForTrip', "Api\PassengerController@payForTrip");
Route::post('/passenger/submitReview', "Api\PassengerController@submitReview");

//trip driver
Route::post('/driver/responseOrder', "Api\DriverController@responseOrder");
Route::post('/driver/arrivedDeparture', "Api\DriverController@arrivedDeparture");

Route::post('/driver/startTrip', "Api\DriverController@startTrip");
Route::post('/driver/finishTrip', "Api\DriverController@finishTrip");
Route::post('/driver/submitReview', "Api\DriverController@submitReview");



Route::post('/test', 'Api\UserController@test');



