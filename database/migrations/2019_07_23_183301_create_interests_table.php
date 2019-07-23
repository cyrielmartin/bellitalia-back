<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInterestsTable extends Migration {

	public function up()
	{
		Schema::create('interests', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('name', 255);
			$table->text('description')->nullable();
			$table->string('link', 255)->nullable();
			$table->decimal('latitude', 10,8);
			$table->decimal('longitude', 11,8);
			$table->integer('city_id')->unsigned();
			$table->integer('bellitalia_id')->unsigned()->nullable();
		});
	}

	public function down()
	{
		Schema::drop('interests');
	}
}