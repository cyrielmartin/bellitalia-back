<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interest extends Model
{

    protected $table = 'interests';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('name', 'description', 'link', 'latitude', 'longitude', 'image', 'city_id', 'bellitalia_id');
    protected $visible = array('name', 'description', 'link', 'latitude', 'longitude', 'image', 'city_id', 'bellitalia_id');

    public function bellitalia()
    {
        return $this->belongsTo('App\Bellitalia');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Tag', 'interest_tag');
    }

    public function city()
    {
        return $this->belongsTo('App\City');
    }

}
