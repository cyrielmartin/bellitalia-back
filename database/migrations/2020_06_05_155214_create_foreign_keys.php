<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('supplements', function(Blueprint $table) {
			$table->foreign('bellitalia_id')->references('id')->on('bellitalias')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('interests', function(Blueprint $table) {
			$table->foreign('supplement_id')->references('id')->on('supplements')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('interests', function(Blueprint $table) {
			$table->foreign('bellitalia_id')->references('id')->on('bellitalias')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('interest_tag', function(Blueprint $table) {
			$table->foreign('tag_id')->references('id')->on('tags')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('interest_tag', function(Blueprint $table) {
			$table->foreign('interest_id')->references('id')->on('interests')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
		Schema::table('images', function(Blueprint $table) {
			$table->foreign('interest_id')->references('id')->on('interests')
						->onDelete('cascade')
						->onUpdate('cascade');
		});
	}

	public function down()
	{
		Schema::table('interests', function(Blueprint $table) {
			$table->dropForeign('interests_bellitalia_id_foreign');
		});
		Schema::table('interests', function(Blueprint $table) {
			$table->dropForeign('interests_supplement_id_foreign');
		});
		Schema::table('interest_tag', function(Blueprint $table) {
			$table->dropForeign('interest_tag_tag_id_foreign');
		});
		Schema::table('interest_tag', function(Blueprint $table) {
			$table->dropForeign('interest_tag_interest_id_foreign');
		});
		Schema::table('images', function(Blueprint $table) {
			$table->dropForeign('images_interest_id_foreign');
		});
		Schema::table('supplements', function(Blueprint $table) {
			$table->dropForeign('supplements_bellitalia_id_foreign');
		});
	}
}
