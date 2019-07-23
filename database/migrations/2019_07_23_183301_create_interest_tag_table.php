<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInterestTagTable extends Migration {

	public function up()
	{
		Schema::create('interest_tag', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->integer('tag_id')->unsigned()->nullable();
			$table->integer('interest_id')->unsigned();
		});
	}

	public function down()
	{
		Schema::drop('interest_tag');
	}
}