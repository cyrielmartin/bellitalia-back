<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterestTag extends Model 
{

    protected $table = 'interest_tag';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = array('tag_id', 'interest_id');
    protected $visible = array('tag_id', 'interest_id');

}