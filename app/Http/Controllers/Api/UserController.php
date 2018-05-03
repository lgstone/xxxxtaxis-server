<?php

namespace App\Http\Controllers\Api;

use App\Model\User;
use App\Model\Driver;
use App\Model\DriverRegistration;
use App\Model\Vehicle;
use App\Model\TripRequest;
use App\Model\Trip;
use App\Model\Review;

use App\Service\UserService;
use App\Service\RedisGeoService;

use Validator;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;
use Carbon\Carbon;

use Illuminate\Support\Facades\Redis;


/**
 * this class is only be used in api interface but not for view
 * and all functions do not need auth
 * 
 */

class UserController extends Controller{
    /**
     * @param 
     * @return 
     */
    public function passengerLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $ret = UserService::login($request->email, $request->password, 'passenger');
        echo json_encode($ret);
    }

    /**
     * @param 
     * @return 
     */
    public function driverLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $ret = UserService::login($request->email, $request->password, 'driver');
        echo json_encode($ret);
    }


    /**
     * @param 
     * @return 
     */
    public function passengerRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'mobile' => 'required|string|unique:users,mobile',
            //'card_no' => 'required|string|unique:users,card_no',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $ret = UserService::passengerRegister($request->all());
        return response()->json($ret);
    }


    /**
     * 1 step register
     * driver register
     * @param 
     * @return 
     */
    public function driverApply(Request $request){

        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'required',
            'driving_license_number' => 'required',
            'driving_license_version' => 'required',
            'driving_license_expires' => 'required',
            'driving_license_class' => 'required',
            'vehicle_plate_number' => 'required',
            'vehicle_brand' => 'required',
            'vehicle_model' => 'required',
            'vehicle_year' => 'required',
            'card_no' => 'required|unique:users,card_no',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $reg = new DriverRegistration();
        $reg->id_ = Uuid::generate()->string;
        $reg->first_name_   = $request->first_name;
        $reg->last_name_    = $request->last_name;
        $reg->email_        = $request->email;
        $reg->mobile_       = $request->mobile;
        $reg->password_     = bcrypt($request->password);
        $reg->driving_license_number_   = $request->driving_license_number;
        $reg->driving_license_version_  = $request->driving_license_version;
        $reg->driving_license_expires_  = $request->driving_license_expires;
        $reg->driving_license_class_    = $request->driving_license_class;
        $reg->vehicle_plate_number_     = $request->vehicle_plate_number;
        $reg->vehicle_brand_ = $request->vehicle_brand;
        $reg->vehicle_model_ = $request->vehicle_model;
        $reg->vehicle_year_  = $request->vehicle_year;
        $reg->request_date_  = Carbon::now()->toDateTimeString();
        $reg->card_no_        = $request->card_no;
        $reg->status_        = config('constant.registerStatus.reviewing');
        $reg->save();

        return response()->json(config('constant.successResponse'));
    }

    /**
     * 2/3 step register
     * driver register step 1
     * @param 
     * @return 
     */
    public function driverBasicSubmit(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:users,email',
            'mobile' => 'required|unique:users,mobile',
            'password' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        $id = Uuid::generate()->string;
        $reg = new DriverRegistration();
        $reg->id_ = $id;
        $reg->first_name_   = $request->first_name;
        $reg->last_name_    = $request->last_name;
        $reg->email_        = $request->email;
        $reg->mobile_       = $request->mobile;
        $reg->password_     = bcrypt($request->password);
        $reg->request_date_  = Carbon::now()->toDateTimeString();
        //$reg->card_no_        = $request->card_no;
        $reg->status_        = config('constant.registerStatus.draft');
        $reg->save();

        return response()->json([
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => [
                'driverRegistrationId' => $id,
            ]
        ]);
    }

    /**
     * 2 step register
     * driver register step 2
     * @param 
     * @return 
     */
    public function moreDriverInfoSubmit(Request $request){
        $validator = Validator::make($request->all(), [
            'driving_license_number' => 'required',
            'driving_license_version' => 'required',
            'driving_license_expires' => 'required',
            'driving_license_class' => 'required',
            'vehicle_plate_number' => 'required',
            'vehicle_brand' => 'required',
            'vehicle_model' => 'required',
            'vehicle_year' => 'required',
            'driver_registration_id' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }


        $reg = DriverRegistration::findOrFail($request->driver_registration_id);
        $reg->driving_license_number_   = $request->driving_license_number;
        $reg->driving_license_version_  = $request->driving_license_version;
        $reg->driving_license_expires_  = $request->driving_license_expires;
        $reg->driving_license_class_    = $request->driving_license_class;
        $reg->vehicle_plate_number_     = $request->vehicle_plate_number;
        $reg->vehicle_brand_ = $request->vehicle_brand;
        $reg->vehicle_model_ = $request->vehicle_model;
        $reg->vehicle_year_  = $request->vehicle_year;
        $reg->request_date_  = Carbon::now()->toDateTimeString();
        $reg->status_        = config('constant.registerStatus.reviewing');
        $reg->save();

        return response()->json(config('constant.successResponse'));
    }



    /**
     * 3 step register
     * driver register step 2
     * @param 
     * @return 
     */
    public function drivingLicenseSubmit(Request $request){

        $validator = Validator::make($request->all(), [
            'driving_license_number' => 'required',
            'driving_license_version' => 'required',
            'driving_license_expires' => 'required',
            'driving_license_class' => 'required',
            'driver_registration_id' => 'required',
            // 'driving_license_front_pic_' => 'required',
            // 'driving_license_back_pic_' => 'required',
        ]);

        if($validator->fails()) {
           return $validator->errors();
        }
        // $file_name_front_pic = Uuid::generate()->string.".png";
        // $file_name_back_pic = Uuid::generate()->string.".png";

        // \Storage::disk('s3')->put($file_name_front_pic, base64_decode($request->driving_license_front_pic_),'public');
        // \Storage::disk('s3')->put($file_name_back_pic, base64_decode($request->driving_license_back_pic_),'public');

        $reg = DriverRegistration::findOrFail($request->driver_registration_id);
        $reg->driving_license_number_   = $request->driving_license_number;
        $reg->driving_license_version_  = $request->driving_license_version;
        $reg->driving_license_expires_  = $request->driving_license_expires;
        $reg->driving_license_class_    = $request->driving_license_class;
        $reg->vehicle_plate_number_     = $request->vehicle_plate_number;
        // $reg->driving_license_front_pic_= \Storage::disk('s3')->url($file_name_front_pic);
        // $reg->driving_license_back_pic_ = \Storage::disk('s3')->url($file_name_back_pic);

        $reg->save();

        return response()->json(config('constant.successResponse'));

    }

    /**
     * 3 step register
     * driver register step 3
     * @param 
     * @return 
     */
    public function drivingVehicleSubmit(Request $request){
        $validator = Validator::make($request->all(), [
            'vehicle_plate_number' => 'required',
            'vehicle_brand' => 'required',
            'vehicle_model' => 'required',
            'vehicle_year' => 'required',
            // 'vehicle_pic' => 'required',
            'driver_registration_id' => 'required',
        ]);
        if($validator->fails()) {
           return $validator->errors();
        }

        // $file_name_vehicle_pic = Uuid::generate()->string.".png";
        // \Storage::disk('s3')->put($file_name_vehicle_pic, base64_decode($request->vehicle_pic_),'public');


        $reg = DriverRegistration::findOrFail($request->driver_registration_id);
        $reg->vehicle_plate_number_     = $request->vehicle_plate_number;
        $reg->vehicle_brand_ = $request->vehicle_brand;
        $reg->vehicle_model_ = $request->vehicle_model;
        $reg->vehicle_year_  = $request->vehicle_year;
        // $reg->vehicle_pic_   = \Storage::disk('s3')->url($file_name_vehicle_pic);

        $reg->status_        = config('constant.registerStatus.reviewing');
        $reg->save();

        return response()->json(config('constant.successResponse'));
    }

    

}