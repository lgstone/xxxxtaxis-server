<?php
namespace App\Service;
use App\Model\User;
use App\Model\Passenger;
use App\Model\TripRequest;
use App\Model\Trip;

use App\Service\RedisGeoService;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;


class TripService{

    /**
     * @param 
     * @return 
     */
    public static function sendRequest($tripRequestId, $history_driver=null){

        $ret = false;

        $tripRequest = TripRequest::findOrFail($tripRequestId);

        //find nearest driver list
        $driver_list = RedisGeoService::findDrivers($tripRequest->src_longitude_, $tripRequest->src_latitude_, 50);
        if(count($driver_list)){
            //find a available driver
            $driver_user_id = null;
            if(empty($history_driver)){
                $driver_user_id = $driver_list[0][0];
                $tripRequest->history_driver_ = $driver_user_id;
                $tripRequest->save();
            }else{
                $history_driver_list = explode(";", $history_driver);
                //check if driver already called
                foreach ($driver_list as $row) {
                    $history_flag = false;
                    foreach ($history_driver_list as $item) {
                        if($item == $row){
                            $history_flag = true;
                            break;
                        }
                    }
                    if($history_flag == false){
                        $driver_user_id = $row;
                        $tripRequest->history_driver_ .= ";".$driver_user_id;
                        $tripRequest->save();
                        break;
                    }
                }
            }
            
            if($driver_user_id){
                $data = [
                    'data' => [
                        'driver_id' => $driver_user_id,
                        'trip_request_id' => $tripRequestId,
                        'src' => [
                            'longitude' => $tripRequest->src_longitude_,
                            'latitude' => $tripRequest->src_latitude_,
                            'location_str' => $tripRequest->departure_,
                        ],
                        'dest' =>[
                            'longitude' => $tripRequest->dest_longitude_,
                            'latitude' => $tripRequest->dest_latitude_,
                            'location_str' => $tripRequest->destination_,
                        ],
                    ],
                    'event' => "request_trip_".$driver_user_id,
                    // 'event' => "test",
                ];
                Redis::publish('driverChannel', json_encode($data));
                $ret = true;
            }

        }
        return $ret;
    }


    /**
     * @param 
     * @return 
     */

    public static function createTripOrder($tripRequestId, $driverId){
        $tripRequest = TripRequest::findOrFail($tripRequestId);
        $trip_id = Uuid::generate()->string;
        $trip = new Trip();
        $trip->id_ = $trip_id;
        $trip->driver_id_ = $driverId;
        $trip->passenger_id_    = $tripRequest->passenger_id_;
        $trip->departure_       = $tripRequest->departure_;
        $trip->src_latitude_    = $tripRequest->src_latitude_;
        $trip->src_longitude_   = $tripRequest->src_longitude_;
        $trip->destination_     = $tripRequest->destination_;
        $trip->dest_latitude_   = $tripRequest->dest_latitude_;
        $trip->dest_longitude_  = $tripRequest->dest_longitude_;
        $trip->status_          = config('constant.tripStatus.notCatch');
        $trip->base_charge_     = config('constant.price.baseCharge');
        $trip->price_per_min_   = config('constant.price.costPerMin');
        $trip->price_minimum_   = config('constant.price.costMinimum');
        $trip->price_per_km_    = config('constant.price.costPerKM');
        $trip->save();
        return $trip_id;
    }


    /**
     * @param 
     * @return 
     */
    public static function calcPrice($trip){
        $dt_start = Carbon::parse($trip->start_time_);
        $dt_end = Carbon::parse($trip->end_time_);
        $calcPrice = $trip->base_charge_ + $trip->price_per_km_ * $trip->distance_ + 
                    $trip->price_per_min_ * $dt_end->diffInMinutes($dt_start);
        $totalPrice = $calcPrice < $trip->price_minimum_ ? $trip->price_minimum_ : $calcPrice;
        return $totalPrice;
    }

}