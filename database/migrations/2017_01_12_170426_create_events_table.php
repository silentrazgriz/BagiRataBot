<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function (Blueprint $table) {
			$table->increments('id');
			$table->string('fbId');
			$table->string('name');
			$table->text('members');
			$table->text('transactions');
			$table->timestamps();

			$table->unique(array('fbId', 'name'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('events');
	}
}
