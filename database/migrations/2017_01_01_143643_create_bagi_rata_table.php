<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBagiRataTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('br_events', function (Blueprint $table) {
			$table->increments('id');
			$table->string('fbId');
			$table->string('event');
			$table->text('members');
			$table->text('purchases');
			$table->text('payments');
			$table->boolean('isActive')->default(false);
			$table->timestamps();

			$table->unique(array('fbId', 'event'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('br_events');
	}
}
