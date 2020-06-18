<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplement extends Model
{
  protected $table = 'supplements';
  public $timestamps = true;

  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $fillable = array('name', 'bellitalia_id', 'image');
  protected $visible = array('name', 'bellitalia_id', 'image');

  public function bellitalia()
  {
      return $this->belongsTo('App\BellItalia', 'bellitalia_id');
  }
  public function interests()
  {
      return $this->hasMany('App\Interest');
  }

}
