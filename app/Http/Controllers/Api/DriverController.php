<?php

namespace App\Http\Controllers\Api;

use App\Model\User;
use App\Model\Driver;
use App\Model\Vehicle;
use App\Model\Review;
use App\Model\TripRequest;
use App\Model\Trip;
use App\Model\Passenger;


use App\Service\UserService;
use App\Service\RedisGeoService;
use App\Service\TripService;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use Illuminate\Support\Facades\Redis;

/**
 * this class is only be used in api interface but not for view
 * and all functions have to be authenticated by using api_token
 * 
 */

class DriverController extends Controller{

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
     * @param 
     * @return 
     */
    public function driverLogout(Request $request){
        $ret = UserService::logout(Auth::user()->email, 'driver');
        return response()->json(config('constant.successResponse'));
    }

    /**
     * @param 
     * @return 
     */
    public function updateLocation(Request $request){
        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',        
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $driver = Auth::user()->driver;
        if($driver->online_status_ == 0){
            $driver->online_status_ = 1;
            $driver->save();
        }
        RedisGeoService::updateDriverLocation(Auth::user()->id, $request->longitude, $request->latitude);

        return response()->json(config('constant.successResponse'));
    }

    /**
     * driver response the passenger request
     * @param 
     * @return 
     */
    public function responseOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'op' => 'required',
            'trip_request_id' => 'required',        
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $tripRequest = TripRequest::findOrFail($request->trip_request_id);
        if($request->op == 'accept'){
            //create order
            $trip_id = TripService::createTripOrder($request->trip_request_id, Auth::user()->driver->id_);
            $passenger = Passenger::findOrFail($tripRequest->passenger_id_);
            $driver = Driver::findOrFail(Auth::user()->driver->id_);
            $driver_user = Auth::user();

            //publish event to passenger
            $data = [
                'data' => [
                    'trip_id' => $trip_id,
                    'op' => 'accept',
                    'driver_info' => [
                        'first_name' => $driver_user->first_name,
                        'last_name' => $driver_user->last_name,
                        'mobile' => $driver_user->mobile,
                        'average_rate' => $driver_user->average_review_rate,
                        'vehicle_plate' => $driver->vehicle->plate_number_,
                        'vehicle_brand' => $driver->vehicle->brand_,
                        'vehicle_model_' => $driver->vehicle->model_,
                    ],
                ],
                'event' => "response_trip_".$passenger->user->id,
            ];
            Redis::publish('passengerChannel', json_encode($data));

        }elseif ($request->op == 'refuse') {
            
            //call another driver
            $ret = TripService::sendRequest($tripRequest->id_, $tripRequest->history_driver_);
            if(!$ret){
                return response()->json(config('constant.unknownErrResponse'));
            }
        }else{
            return response()->json(config('constant.unknownErrResponse'));
        }

        //set driver status
        Auth::user()->driver->online_status_ = config('constant.driverStatus.onTrip');
        Auth::user()->driver->save();
        
        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => [
                'trip_id' => $trip_id,
            ],
        ]);

    }

    /**
     * when driver pick up the passenger
     * @param 
     * @return 
     */
    public function startTrip(Request $request){
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',       
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $trip_id = $request->trip_id;
        $trip = Trip::findOrFail($trip_id);

        $passenger = Passenger::findOrFail($trip->passenger_id_);
        
        $trip->start_time_ = Carbon::now()->toDateTimeString();
        $trip->status_ = config('constant.tripStatus.onTrip');
        $trip->save();
        //notify passenger
        $data = [
            'data' => [
                'trip_id' => $trip_id,
            ],
            'event' => "start_trip_".$passenger->user->id,
        ];
        //send message to passenger
        Redis::publish('passengerChannel', json_encode($data));
        return response()->json(config('constant.successResponse'));
    }



    /**
     * when driver finish trip
     * @param 
     * @return 
     */
    public function finishTrip(Request $request){
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
            'distance' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }
        
        $trip = Trip::findOrFail($request->trip_id);
        $end_time = Carbon::now();
        $trip->end_time_ = $end_time->toDateTimeString();
        $trip->distance_ = $request->distance;
        $passenger = Passenger::findOrFail($trip->passenger_id_);

        //calc price
        $total_price = TripService::calcPrice($trip);
        $trip->total_price_ = $total_price;
        $trip->status_ = config('constant.tripStatus.arrived');
        $trip->save();

        $data = [
            'data' => [
                'trip_id' => $request->trip_id,
                'total_price' => $total_price,
            ],
            'event' => "finish_trip_".$passenger->user->id,
        ];
        //send message to passenger
        Redis::publish('passengerChannel', json_encode($data));

        //set driver status
        Auth::user()->driver->online_status_ = config('constant.driverStatus.online');
        Auth::user()->driver->save();
        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
        ]);

    }

    /**
     * submit the feedback for passenger after a trip
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
        $review->for_driver_ = 0;
        $review->save();

        $userService->calcPassengerOverallRate($trip->passenger_id_);

        return response()->json(config('constant.successResponse'));
    }
	
    /**
     * get the driver profile info
     * @param 
     * @return 
     */
    public function getProfile(Request $request){
        $driver = Auth::user()->driver;
        if($driver){
            $ret = array(
                'status' => config('constant.responseStatus.success.code'),
                'errMsg' => config('constant.responseStatus.success.errMsg'),
                'data' => Auth::user()->toArray(),
            );
            return $ret;
        }
    }

    /**
     * send notification to passenger where driver arrived the departure
     * @param 
     * @return 
     */
    public function arrivedDeparture(Request $request){
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }
        $driver = Auth::user()->driver;
        $passenger = Trip::findOrFail($request->trip_id);

        $data = [
            'data' => [
                'trip_id' => $request->trip_id,
            ],
            'event' => "arrive_departure_".$passenger->user->id,
        ];

        Redis::publish('passengerChannel', json_encode($data));
        return response()->json(config('constant.successResponse'));
    }


    /**
     * driver go offline status
     * @param 
     * @return 
     */
    public function goOffline(Request $request){
        $driver = Auth::user()->driver;
        $driver->online_status_ = config('constant.driverStatus.offline');;
        $driver->save();
        RedisGeoService::deleteUserLocation(Auth::user()->id, 'driver');

        return response()->json(config('constant.successResponse'));
    }

    /**
     * driver go online status
     * @param 
     * @return 
     */
    public function goOnline(Request $request){
        $driver = Auth::user()->driver;
        $driver->online_status_ = config('constant.driverStatus.online');;
        $driver->save();

        return response()->json(config('constant.successResponse'));
    }

    /**
     * get trips
     * @param 
     * @return 
     */
    public function getTrip(Request $request){
        $trips = Trip::where('driver_id_', Auth::user()->driver->id_)->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => [
                'trips' => $trips->toArray(),
            ]
        ]);
    }



}