<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{

    protected $table = 'tags';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('name', 'created_at');
    protected $visible = array('name', 'created_at');

    public function interests()
    {
        return $this->belongsToMany('App\Interest', 'interest_tag');
    }

}
