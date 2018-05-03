<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'vehicle';

    protected $primaryKey = 'id_';

    public $incrementing = false;

    public function driver()
    {
        return $this->belongsTo('App\Model\Driver', 'driver_id_', 'id_');
    }

}
