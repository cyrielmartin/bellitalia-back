<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBellitaliasTable extends Migration {

	public function up()
	{
		Schema::create('bellitalias', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('number')->unsigned();
			$table->datetime('publication');
			$table->string('image');
		});
	}

	public function down()
	{
		Schema::drop('bellitalias');
	}
}
