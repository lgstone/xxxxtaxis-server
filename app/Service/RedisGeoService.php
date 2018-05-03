<?php
namespace App\Service;
use App\Model\User;
use App\Model\Passenger;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class RedisGeoService{
    /**
     * @param 
     * @return 
     */
    public static function updatePassengerLocation($userId, $longitude, $latitude){
        Redis::geoadd('passenger', $longitude, $latitude, $userId);
    }

    /**
     * @param 
     * @return 
     */
    public static function updateDriverLocation($userId, $longitude, $latitude){
        Redis::geoadd('driver', $longitude, $latitude, $userId);
    }

    /**
     * @param 
     * @return 
     */
    public static function getPassengerLocation($passengerId){
        return Redis::geopos('passenger', $passengerId);
    }

    /**
     * @param 
     * @return 
     */
    public static function getDriverLocation($userId){
        return Redis::geopos('driver', $userId);
    }

    /**
     * @param 
     * @return 
     */
    public static function findDrivers($longitude, $latitude, $distance){
        return Redis::georadius('driver', $longitude, $latitude, $distance, 'km', 'WITHDIST', 'WITHCOORD');
    }


    /**
     * @param 
     * @return 
     */
    public static function deleteUserLocation($userId, $role){
        if($role == 'passenger'){
            Redis::zrem('driver', $userId);
        }else if($role == 'driver'){
            Redis::zrem('passenger', $userId);
        }
        return;
    }




}