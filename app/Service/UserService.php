<?php
namespace App\Service;
use App\Model\User;
use App\Model\Passenger;
use App\Model\Driver;
use App\Model\Review;

use Illuminate\Support\Facades\DB;
use Webpatser\Uuid\Uuid;


class UserService
{

    /**
     * @param 
     * @return 
     */
    public static function getUserByApiToken($token){
        return User::where('api_token', $token);
    }

    /**
     * @param 
     * @return 
     */
    public static function login($username, $passwd, $role){
        if($role == 'driver'){
            $bitmap = pow(2, config('constant.rolesBitmap.Driver'));
        }elseif ($role == 'passenger') {
            $bitmap = pow(2, config('constant.rolesBitmap.Passenger'));
        }
        $code = config('constant.responseStatus.authenticateErr.code');
        $msg = config('constant.responseStatus.authenticateErr.errMsg');
        $user = User::where('email', $username)
                ->whereRaw('role & '.$bitmap .'='. $bitmap)
                ->first();
        $api_token = "";
        $user_id = "";

        if($user){
            $ret = \Hash::check($passwd, $user->password);
            if($ret){
                if($role == 'driver'){
                    $user->driver->online_status_ = config('constant.driverStatus.offline');
                    $user->driver->save();
                    RedisGeoService::deleteUserLocation($user->id, 'driver');
                }
                $user->api_token = str_random(60);
                $user->save();
                $code = config('constant.responseStatus.success.code');
                $msg = config('constant.responseStatus.success.errMsg');
                $api_token = $user->api_token;
                $user_id = $user->id;
            }
        }
        $ret = array(
            'status' => $code,
            'errMsg' => $msg,
            'data' => [
                'api_token' => $api_token,
                'user_id' => $user_id,
            ],
        );
        return $ret;
    }

    /**
     * @param 
     * @return 
     */
    public static function logout($username, $role){
        $code = config('constant.responseStatus.unknownErr.code');
        $msg = config('constant.responseStatus.unknownErr.errMsg');
        $user = User::where('email', $username)->first();
        if($user){
            $user->api_token = str_random(60);
            $user->save();
            $code = config('constant.responseStatus.success.code');
            $msg = config('constant.responseStatus.success.errMsg');
            //remove location info
            RedisGeoService::deleteUserLocation($user->id, $role);
        }       
        $ret = array(
            'status' => $code,
            'errMsg' => $msg,
        );
        return $ret;
    }

    /**
     * @param 
     * @return 
     */
    public static function passengerRegister($data){
        $user = new User();
        $user_id = Uuid::generate()->string;
        $user->id           = $user_id;
        $user->email        = $data['email'];
        $user->password     = bcrypt($data['password']);
        $user->first_name   = $data['first_name'];
        $user->last_name    = $data['last_name'];
        $user->mobile       = $data['mobile'];
        $user->role         = 1;
        $user->api_token    = str_random(60);
        //$user->card_no      = $data['card_no'];
        $user->save();

        $passenger = new Passenger();
        $passenger->id_ = Uuid::generate()->string;
        $passenger->user_id_ = $user_id;
        $passenger->user_level_ = 1;
        $passenger->save();

        $ret = array(
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => array(),
        );
        return $ret;
    }

    public function calcPassengerOverallRate($passengerId){
        $reviews = Review::where('passenger_id_', $passengerId)->get();
        $review_count = $reviews->count();
        $review_rate_sum = 0;
        foreach ($reviews as $review) {
            $review_rate_sum += config('constant.maxReviewRate') - $review->rate_;
        }
        $overall_rate = number_format($review_rate_sum/$review_count, 2);

        $passenger = Passenger::findOrFail($passengerId);
        $passenger->overall_rate_ = config('constant.maxReviewRate') - $overall_rate;
        $passenger->save();

        return;
    }

    public function calcDriverOverallRate($driverId){
        $reviews = Review::where('driver_id_', $driverId)->get();
        $review_count = $reviews->count();
        $review_rate_sum = 0;
        foreach ($reviews as $review) {
            $review_rate_sum += config('constant.maxReviewRate') - $review->rate_;
        }
        $overall_rate = number_format($review_rate_sum/$review_count, 2);

        $driver = Driver::findOrFail($driverId);
        $driver->overall_rate_ = config('constant.maxReviewRate') - $overall_rate;
        $driver->save();

        return;
    }


}