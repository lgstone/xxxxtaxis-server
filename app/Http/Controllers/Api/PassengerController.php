<?php

namespace App\Http\Controllers\Api;

use App\Model\User;
use App\Model\Driver;
use App\Model\Vehicle;
use App\Model\TripRequest;
use App\Model\Trip;
use App\Model\Review;
use App\Model\Passenger;

use App\Service\UserService;
use App\Service\RedisGeoService;
use App\Service\TripService;
use App\Service\PaymentService;


use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;


/**
 * this class is only be used in api interface but not for view
 * and all functions have to be authenticated by using api_token
 * 
 */

class PassengerController extends Controller{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * logout
     * @param 
     * @return 
     */
    public function passengerLogout(Request $request){
        $ret = UserService::logout(Auth::user()->email, 'passenger');
        return response()->json($ret);
    }

    /**
     * update location and get the driver list in the response
     * @param 
     * @return 
     */
    public function updateLocationAndGetDriverList(Request $request){
        //this way return a html render
        // $this->validate($request, [
        //    'longitude' => 'required',
        //    'latitude' => 'required',
        // ]);

        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',        
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        RedisGeoService::updatePassengerLocation(Auth::user()->id, $request->longitude, $request->latitude);
        $data = RedisGeoService::findDrivers($request->longitude, $request->latitude, 50);
        $ret_data = [];
        foreach ($data as $item) {
            $ret_data[] = [
                'driver_id' => $item[0],
                'distance' => floatval($item[1]),
                'location' => [
                    'latitude' => floatval($item[2][1]),
                    'longitude' => floatval($item[2][0]),
                ],
            ];
        }
        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => $ret_data,
        ]);
    }



    /**
     * passenger request an order 
     * @param 
     * @return 
     */
    public function requestOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'dep_string' => 'required|string',
            'src_longitude' => 'required',
            'src_latitude' => 'required',
            'dest_string' => 'required|string',
            'dest_longitude' => 'required',
            'dest_latitude' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $trip_request_id = Uuid::generate()->string;
        //save request to database
        $tripRequest = new TripRequest();
        $tripRequest->id_ = $trip_request_id;

        $tripRequest->departure_ = $request->dep_string;
        $tripRequest->src_longitude_ = $request->src_longitude;
        $tripRequest->src_latitude_ = $request->src_latitude;

        $tripRequest->destination_ = $request->dest_string;
        $tripRequest->dest_longitude_ = $request->dest_longitude;
        $tripRequest->dest_latitude_ = $request->dest_latitude;

        $tripRequest->passenger_id_ = Auth::user()->passenger->id_;
        $tripRequest->status_ = config('constant.requestStatus.Requesting');
        $tripRequest->save();

        //start to find and request driver
        $ret = TripService::sendRequest($trip_request_id);

        if($ret){
            return response()->json([
                'status' => config('constant.responseStatus.success.code'),
                'errMsg' => config('constant.responseStatus.success.errMsg'),
                'data' => [
                    'trip_request_id' => $trip_request_id,
                ],
            ]);
        }      
        return response()->json(config('constant.unknownErrResponse'));
    }


    /**
     * submit the feedback for driver after a trip
     * @param 
     * @return 
     */
    public function submitReview(Request $request, UserService $userService){
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'rate' => 'required',
            'comments' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $trip = Trip::findOrFail($request->trip_id);

        $review = new Review();
        $review->id_ = Uuid::generate()->string;
        $review->trip_id_ = $request->trip_id;
        
        //for calc easier
        $review->driver_id_ = $trip->driver_id_;
        $review->passenger_id_ = $trip->passenger_id_;
        $review->rate_ = config('constant.maxReviewRate') - $request->rate;
        $review->comments_ = $request->comments;
        $review->for_driver_ = 1;
        $review->save();

        //calc overall rate
        $userService->calcDriverOverallRate($trip->driver_id_);

        return response()->json(config('constant.successResponse'));    
    }


    /**
     * get passenger profile
     * @param 
     * @return 
     */
    public function getProfile(Request $request){
        $passenger = Auth::user()->passenger;
        if($passenger){
            $ret = array(
                'status' => config('constant.responseStatus.success.code'),
                'errMsg' => config('constant.responseStatus.success.errMsg'),
                'data' => Auth::user()->toArray(),
            );
            return $ret;
        }
        return response()->json(config('constant.unknownErrResponse'));
    }


    /**
     * after trip payment
     * @param 
     * @return 
     */
    public function payForTrip(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'trip_id' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $trip = Trip::findOrFail($request->trip_id);
        $driver = Driver::findOrFail($trip->driver_id_);
        $passenger = Passenger::findOrFail($trip->passenger_id_);
        if($request->type != 'cash'){
            if($passenger->user->balance >= $trip->total_price_){
                DB::transaction(function() use ($passenger, $driver, $trip) {
                    $passenger->user->balance -= $trip->total_price_;
                    $driver->user->balance += $trip->total_price_;
                    $passenger->user->save();
                    $driver->user->save();
                    $trip->status_ = config('constant.tripStatus.finished');
                    $trip->save();
                });
            }else{
                //balance not enough, use balance and left use gateway
                DB::transaction(function() use ($passenger, $driver, $trip) {
                    $balance_amount = $passenger->user->balance;
                    if(!PaymentService::payByGateway($trip->total_price_-$balance_amount)){
                        throw new Exception("pay failed", 1);
                    }
                    $passenger->user->balance = 0;
                    $driver->user->balance += $balance_amount;
                    $passenger->user->save();
                    $driver->user->save();
                    $trip->status_ = config('constant.tripStatus.finished');
                    $trip->save();
                });
            }
        }else{
            //todo
            $trip->status_ = config('constant.tripStatus.finished');
            $trip->save();
        }
        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
        ]);
    }

    /**
     * get trips
     * @param 
     * @return 
     */
    public function getTrip(Request $request){
        $trips = Trip::where('passenger_id_', Auth::user()->passenger->id_)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => [
                'trips' => $trips->toArray(),
            ]
        ]);
    }


}