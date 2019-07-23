<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model 
{

    protected $table = 'Places';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('region', 'city', 'monument', 'latitude', 'longitude', 'description', 'issue', 'published', 'link');
    protected $visible = array('region', 'city', 'latitude', 'longitude', 'description', 'issue', 'published', 'link');

}