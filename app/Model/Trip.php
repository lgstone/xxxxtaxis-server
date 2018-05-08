<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'trip';

    protected $primaryKey = 'id_';

    public $incrementing = false;


    public function passenger(){
        return $this->hasOne('App\Model\Passenger', 'id_', 'passenger_id_');
    }

    public function driver(){
        return $this->hasOne('App\Model\Driver', 'id_','driver_id_');
    }

    public function passengerRating(){
        return $this->hasOne('App\Model\Review', 'trip_id_')->where('for_driver_', 0);
    }

    public function driverRating(){
        return $this->hasOne('App\Model\Review', 'trip_id_')->where('for_driver_', 1);
    }

}
