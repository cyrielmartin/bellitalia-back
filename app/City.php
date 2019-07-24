<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{

    protected $table = 'cities';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('name', 'region_id');
    protected $visible = array('name', 'region_id');

    public function interests()
    {
        return $this->hasMany('App\Interest');
    }

    public function region()
    {
        return $this->belongsTo('App\Region');
    }

}
