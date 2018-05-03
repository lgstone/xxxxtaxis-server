<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'driver';

    protected $primaryKey = 'id_';

    public $incrementing = false;

    public function user()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id_');
    }
    public function vehicle()
    {
        return $this->belongsTo('App\Model\Vehicle', 'id_', 'driver_id_');
    }
}
