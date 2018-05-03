<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passenger extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'passenger';

    protected $primaryKey = 'id_';

    public $incrementing = false;

    public function user()
    {
        return $this->hasOne('App\Model\User', 'id', 'user_id_');
    }

}
