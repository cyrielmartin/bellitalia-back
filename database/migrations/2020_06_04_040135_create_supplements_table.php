<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplementsTable extends Migration
{
  /**
  * Run the migrations.
  *
  * @return void
  */
  public function up()
  {
    Schema::create('supplements', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->timestamps();
      $table->softDeletes();
      $table->string('name', 50);
      $table->integer('bellitalia_id')->unsigned();
      $table->string('image');
    });
  }

  /**
  * Reverse the migrations.
  *
  * @return void
  */
  public function down()
  {
    Schema::dropIfExists('supplements');
  }
}
