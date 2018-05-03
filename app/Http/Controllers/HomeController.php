<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Model\User;
use App\Model\Trip;
use App\Model\Vehicle;
use App\Model\Driver;

use App\Model\DriverRegistration;

use Webpatser\Uuid\Uuid;
use App\Service\HtmlHelperService;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application homepage.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the passneger info.
     *
     * @return \Illuminate\Http\Response
     */
    public function listPassenger(Request $request)
    {
        $bitmap = pow(2, config('constant.rolesBitmap.Passenger'));
        $users = User::whereRaw('role & '.$bitmap .'='. $bitmap)->orderBy('created_at', 'desc')->get();
        return view('passenger', [ 'users' => $users]);
    }

    /**
     * Show the driver info.
     *
     * @return \Illuminate\Http\Response
     */
    public function listDriver()
    {
        $bitmap = pow(2, config('constant.rolesBitmap.Driver'));
        $users = User::whereRaw('role & '.$bitmap .'='. $bitmap)->orderBy('created_at', 'desc')->get();
        return view('driver', [ 'users' => $users]);
    }


    /**
     * Show the trip info.
     *
     * @return \Illuminate\Http\Response
     */
    public function listTrip()
    {
        $trips = Trip::orderBy('created_at', 'desc')->get();
        return view('trip', [ 'trips' => $trips]);
    }

    /**
     * Show the trip info.
     *
     * @return \Illuminate\Http\Response
     */
    public function listTripByPassengerUser($userid)
    {
        $user = User::findOrFail($userid);
        if(isset($user->passenger->id_)){
            $trips = Trip::where('passenger_id_', $user->passenger->id_)->orderBy('created_at', 'desc')->get();
        }else {
            $trips = [];
        }
        
        return view('trip', [ 
            'trips' => $trips, 
            'user' => $user,
            'type' => 'passenger',
        ]);
    }

    /**
     * Show the trip info.
     *
     * @return \Illuminate\Http\Response
     */
    public function listTripByDriverUser($userid)
    {
        $user = User::findOrFail($userid);
        if(isset($user->driver->id_)){
            $trips = Trip::where('driver_id_', $user->driver->id_)->orderBy('created_at', 'desc')->get();
        }else {
            $trips = [];
        }
        
        return view('trip', [ 
            'trips' => $trips, 
            'user' => $user,
            'type' => 'driver',
        ]);
    }



    /**
     * Show the vehivle info.
     *
     * @return \Illuminate\Http\Response
     */
    public function listVehicle()
    {
        $vehicles = Vehicle::orderBy('created_at', 'desc')->get();
        return view('vehicle', [ 'vehicles' => $vehicles]);
    }

    /**
     * Show the register info.
     *
     * @return \Illuminate\Http\Response
     */
    public function driverRegister()
    {
        $regs = DriverRegistration::where('status_', config('constant.registerStatus.reviewing'))->orderBy('created_at', 'desc')->get();

        return view('driverReg', [ 'regs' => $regs]);
    }

    /**
     * Show the register info.
     *
     * @return \Illuminate\Http\Response
     */
    public function driverApplyHistory()
    {
        $regs = DriverRegistration::where('status_', '!=' ,config('constant.registerStatus.reviewing'))->orderBy('created_at', 'desc')->get();

        return view('driverRegHistory', [ 'regs' => $regs]);
    }


    /**
     * operate of the driver register req.
     *
     * @return \Illuminate\Http\Response
     */
    public function driverRegisterOperate(Request $request)
    {
        $reg = DriverRegistration::findOrFail($request->id);
        if($request->op == config('constant.driverRegisterOperate.approve')){
            DB::transaction(function() use ($reg) {
                $reg->status_ = config('constant.registerStatus.approved');
                $user = new User();
                $user_id = Uuid::generate()->string;
                $user->id           = $user_id;
                $user->email        = $reg->email_;
                $user->password     = $reg->password_;
                $user->first_name   = $reg->first_name_;
                $user->last_name    = $reg->last_name_;
                $user->mobile       = $reg->mobile_;
                $user->role         = 1<<config('constant.rolesBitmap.Driver');
                $user->api_token    = str_random(60);
                $user->card_no      = $reg->card_no_;
                $user->save();

                $driver = new Driver();
                $driver->id_ = Uuid::generate()->string;
                $driver->driving_license_number_    = $reg->driving_license_number_;
                $driver->driving_license_version_   = $reg->driving_license_version_;
                $driver->driving_license_expires_   = $reg->driving_license_expires_;
                $driver->driving_license_class_     = $reg->driving_license_class_;
                $driver->driving_license_front_pic_ = $reg->driving_license_front_pic_;
                $driver->driving_license_back_pic_  = $reg->driving_license_back_pic_;
                $driver->user_id_                   = $user_id;
                $driver->save();

                $vehicle = new Vehicle();
                $vehicle->id_ = Uuid::generate()->string;
                $vehicle->driver_id_    = $driver->id_;
                $vehicle->plate_number_ = $reg->vehicle_plate_number_;
                $vehicle->brand_        = $reg->vehicle_brand_;
                $vehicle->model_        = $reg->vehicle_model_;
                $vehicle->year_         = $reg->vehicle_year_;
                $vehicle->vehicle_pic_  = $reg->vehicle_pic_;
                $vehicle->status_       = config('constant.vehicleStatus.Ready');
                $vehicle->save();
            });
        }elseif($request->op == config('constant.driverRegisterOperate.decline')){
            $reg->status_ = config('constant.registerStatus.declined');
        }else{
            //do nothing
        }
        $reg->save();
        $ret = array(
            'status' => config('constant.responseStatus.success.code'),
            'errMsg' => config('constant.responseStatus.success.errMsg'),
            'data' => array(
                'redirect' => '/channel/driverRegister',
            ),
        );

        return json_encode($ret);
    }

    public function ajaxGetTripDetail(Request $request, $tripId){
        return Trip::findOrFail($tripId)->toJson();

    }




}
