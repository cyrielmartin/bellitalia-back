<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePlacesTable extends Migration {

	public function up()
	{
		Schema::create('Places', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->softDeletes();
			$table->string('region', 30);
			$table->string('city', 30);
			$table->string('monument', 40)->nullable();
			$table->decimal('latitude', 10);
			$table->decimal('longitude', 10);
			$table->string('description')->nullable();
			$table->integer('issue')->unsigned();
			$table->datetime('published');
			$table->text('link')->nullable();
		});
	}

	public function down()
	{
		Schema::drop('Places');
	}
}