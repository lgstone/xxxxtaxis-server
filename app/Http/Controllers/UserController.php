<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Model\Driver;
use App\Model\Vehicle;
use App\Model\DriverRegistration;

use App\Service\UserService;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Webpatser\Uuid\Uuid;

class UserController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param 
     * @return 
     */
    public function ajaxLogin(Request $request){

    }

    /**
     * @param 
     * @return 
     */
    public function ajaxGetDriverDetail(Request $request, $userid){
        return Driver::where('user_id_', $userid)->first()->toJson();
    }

    /**
     * @param 
     * @return 
     */
    public function ajaxGetVehicleDetailByUser(Request $request, $userid){
        $driver = Driver::where('user_id_', $userid)->first();
        return Vehicle::where('driver_id_', $driver->id_)->first()->toJson();
    }

    /**
     * @param 
     * @return 
     */
    public function ajaxGetRegistrationDetail(Request $request, $regId){
        $reg = DriverRegistration::findOrFail($regId);
        return $reg->toJson();
    }




}